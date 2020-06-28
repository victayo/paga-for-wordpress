<div id="bills" ng-app="bills" ng-controller="billController">
    <div class="payment_container">
        <div class="payment_block">
            <form>
                <div class="form_row">
                    <label for="">Email</label>
                    <input type="email" name="" id="" ng-model="email" class="form-control">
                </div>

                <div class="form_row">
                    <label for="">Bill</label>
                    <select ng-model="bill" class="form-control" ng-change="getMerchantServices()">
                        <option value="">Select bill to pay</option>
                        <option ng-repeat="merchant in merchants" value="{{merchant.uuid}}">{{merchant.name}}</option>
                    </select>
                </div>

                <div ng-if="merchantServices.length" class="form_row">
                    <label for="">Option</label>
                    <select class="form-control" ng-options="ms as ms.name for ms in merchantServices" 
                        ng-model="selectedService.service" ng-change="showAmount()">
                    </select>
                </div>

                <div>
                    <label for="merchantReference">Reference Number</label>
                    <input type="text" name="merchantReference" ng-model="merchantReference" class="form-control">
                </div>

                <div ng-if="amount" class="form_row">
                    <label for="">Amount (NGN)</label>
                    <input type="text" name="amount" ng-model="amount" class="form-control">
                </div>
                <?php if($data['public_key']): ?>
                    <div ng-class="{ button_hide: !amount }">
                        <script src="<?= $data['checkout_url'] ?>"
                        data-public_key="<?= $data['public_key'] ?>"
                        data-amount={{amount}}
                        data-currency="NGN"
                        data-payment_reference="<?= $data['reference'] ?>"
                        data-account_number="<?= $data['account_number'] ?>"
                        data-product_description="Bill Payment"
                        data-email={{email}}
                        data-product_codes={{service.code}}
                        data-display_image="https://secureservercdn.net/45.40.148.147/j8y.ce5.myftpupload.com/wp-content/uploads/2020/05/cropped-Logo-1-1.jpg-New-1-1.jpg"
                        data-display_name="Bill Payment Display"
                        data-display_tagline="Walk the Talk"
                        data-button_label="Pay With Paga">
                        </script>
                    </div>
                <?php endif ?>
            </form>
        </div>
    </div>
    
    <script>
        var app = angular.module('bills', []);
        app.controller('billController', function($scope, $http) {
            $scope.merchants = [];
            $scope.merchantServices = [];
            $scope.amount = 0;
            $scope.email = '';
            $scope.bill = '';
            $scope.merchantReference = '';
            $scope.loading = false;
            $scope.selectedService = null;
            $scope.service = "";
            $scope.service1 = "";
            $scope.selectedService = {
                service: ''
            };

            $http.get("<?= $data['merchants_url']?>").then((response) => {
                let responseData = response.data;
                if(responseData.responseCode == 0 && responseData.message == 'Success'){
                    $scope.merchants = responseData.merchants;
                }
                $scope.loading = false;
            });

            $scope.getMerchantServices = function(){
                $scope.loading = true;
                $scope.merchantServices = [];
                $scope.merchantReference = '';
                $scope.amount = 0;
                let url = "<?= $data['merchant_services_url'] ?>";
                let data = {merchant: $scope.bill};
                $http.post(url, data).then((response) => {
                    let responseData = response.data;
                    if(responseData.responseCode == 0 && responseData.message == 'Success'){
                        $scope.merchantServices = responseData.services;
                        if($scope.merchantServices.length){
                            $scope.selectedService.service = $scope.merchantServices[0];
                            $scope.showAmount();
                        }
                    }
                    $scope.loading = false;
                });
            }

            $scope.showAmount = function(ms){
                $scope.amount = $scope.selectedService.service.price;
            }
        });
    </script>
</div>