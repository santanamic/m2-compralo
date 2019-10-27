define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function($, Component, urlBuilder) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Compralo_Payments/payment/compraloredirect',
                redirectAfterPlaceOrder: false
            },
            afterPlaceOrder: function(url) {
                window.location.replace(urlBuilder.build('compralo/payment/redirect/'));
            }
        });
    }
);