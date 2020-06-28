<div id="airtime" ng-app="airtime" ng-controller="airtimeController">
    <div class="payment_container">
        <div class="payment_block">
            <form>
                <!-- <div class="form_row">
                    <label>Full Name</label>
                    <input type="text" ng-model="fullname" class="form-control">
                </div> -->
                <div class="form_row">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" ng-model="email" class="form-control">
                </div>
                <div class="form_row">
                    <label for="phone">Phone number</label>
                    <input type="text" name="phone" id="phone" ng-model="phone" class="form-control">
                </div>
                <div class="form_row">
                    <label for="amount">Amount (NGN)</label>
                    <input type="number" name="amount" id="amount" ng-model="amount" class="form-control">
                </div>
                <div class="form_row">
                    <label for="network">Network</label>
                    <input type="text" ng-model="network" name="network" id="network" class="form-control">
                </div>
                <?php if($data['public_key']): ?>
                    <script src="<?= $data['checkout_url'] ?>" 
                    data-public_key="<?= $data['public_key'] ?>" 
                    data-amount="1000" data-currency="NGN" 
                    data-payment_reference="<?= $data['reference'] ?>" 
                    data-account_number="<?= $data['account_number'] ?>" 
                    data-product_description="Mobile airtime recharge" 
                    data-phone_number={{phone}} 
                    data-email={{email}}
                    data-product_codes="XBR-652394" 
                    data-display_image="https://secureservercdn.net/45.40.148.147/j8y.ce5.myftpupload.com/wp-content/uploads/2020/05/cropped-Logo-1-1.jpg-New-1-1.jpg" data-display_name="Airtime Recharge" data-display_tagline="Walk the Talk" data-button_label="Pay With Paga">
                    </script>
                <?php endif ?>
            </form>
        </div>
    </div>

</div>
<script>
    var app = angular.module('airtime', []);
    app.controller('airtimeController', function($scope) {
        // $scope.fullname = "";
        $scope.amount = 100;
        $scope.email = '';
        $scope.phone = ''
    });
</script>