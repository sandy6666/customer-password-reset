/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'jquery',
    'ko',
    'mage/translate',
    'mage/template',
    'underscore',
    'Magento_Ui/js/modal/alert',
], function (Component, confirm, $, ko, $t, template, _, alert) {

    'use strict';

    return Component.extend({
        /**
         * Initialize Component
         */
        initialize: function () {
            var self = this,
                content;

            this._super();

            content = '<div class="message message-warning">' + self.content + '</div>' + self.passwordField;


            /**
             * Confirmation popup
             *
             * @returns {Boolean}
             */
            window.rpaPopup = function () {
                confirm({
                    url: self.url,
                    title: self.title,
                    content: content,
                    modalClass: 'confirm rpa-confirm',
                    actions: {
                        /**
                         * Confirm action.
                         */
                        confirm: function () {

                            let formKey = $('input[name="form_key"]').val(),
                                params = {};

                            if (formKey) {
                                params.form_key = formKey;
                            }
                            if(self.customerId) {
                                params.customerId = self.customerId;
                            }
                            if($('.password-field').val()) {
                                params.newPassword = $('.password-field').val();
                            }
                            // jscs:enable requireCamelCaseOrUpperCaseIdentifiers
                            $('.password-input-field').validate();

                            if($('.password-input-field').valid()) {
                                $.ajax({
                                    url: self.url,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: params,
                                    showLoader: true,

                                    /**
                                     * Open redirect URL in new window, or show messages if they are present
                                     *
                                     * @param {Object} data
                                     */
                                    success: function (data) {
                                        var messages = data.messages || [];

                                        if (data.message) {
                                            messages.push(data.message);
                                        }

                                        if (messages.length) {
                                            messages = messages.map(function (message) {
                                                return _.escape(message);
                                            });

                                            alert({
                                                content: messages.join('<br>')
                                            });
                                        }
                                    },

                                    /**
                                     * Show XHR response text
                                     *
                                     * @param {Object} jqXHR
                                     */
                                    error: function (jqXHR) {
                                        alert({
                                            content: _.escape(jqXHR.responseText)
                                        });
                                    }
                                });
                            } else {
                                $('.password-input-field').validate().form();
                            }
                        }
                    },
                    buttons: [{
                        text: $t('Cancel'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Click handler.
                         */
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }, {
                        text: $t('Reset Password'),
                        class: 'action-primary action-accept',

                        /**
                         * Click handler.
                         */
                        click: function (event) {
                            if($('.password-input-field').valid()) {
                                this.closeModal(event, true);
                            } else {
                                $('.password-input-field').validate().form();
                            }
                        }
                    }]
                });

                return false;
            };
        }
    });
});
