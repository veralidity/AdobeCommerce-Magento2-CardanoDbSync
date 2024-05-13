/**
 * Veralidity, LLC
 *
 * @package    Veralidity
 * @category   CardanoDbSync
 * @copyright  Copyright © 2024 Veralidity, LLC
 * @license    https://www.veralidity.com/license/
 * @author     Veralidity, LLC <veralidity@protonmail.com>
 */

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui'
], function ($, alert) {
    'use strict';

    $.widget('mage.testCardanoDbSyncConnection', {
        options: {
            url: '',
            elementId: '',
            successText: '',
            failedText: '',
            fieldMapping: ''
        },

        /**
         * Bind handlers to events
         */
        _create: function () {
            this._on({
                'click': $.proxy(this._connect, this)
            });
        },

        /**
         * Method triggers an AJAX request to check search engine connection
         * @private
         */
        _connect: function () {
            var result = this.options.failedText,
                element =  $('#' + this.options.elementId),
                blockchainElement =  $('#blockchain-' + this.options.elementId),
                self = this,
                params = {},
                msg = '',
                fieldToCheck = this.options.fieldToCheck || 'success';

            element.removeClass('success').addClass('fail');
            $.each(JSON.parse(this.options.fieldMapping), function (key, el) {
                params[key] = $('#' + el).val();
            });
            $.ajax({
                url: this.options.url,
                showLoader: true,
                data: params,
                headers: this.options.headers || {}
            }).done(function (response) {
                console.log(response);
                if (response[fieldToCheck]) {
                    element.removeClass('fail').addClass('success');
                    result = self.options.successText;

                    // Add Blockchain Data
                    blockchainElement.empty();
                    // Add Cardano DbSync PostgreSQL Sync Percentage
                    if (response['cardano_dbsync'].hasOwnProperty('sync_percent')) {
                        // Create a <li> element for each key-value pair using jQuery
                        var h4Element = $('<h4>Percent Synced: ' + response['cardano_dbsync']['sync_percent'] + '%</h4>');
                        
                        // Append the <li> element to the <ul> using jQuery
                        blockchainElement.append(h4Element);
                    }
                    // Create a <ul> element using jQuery
                    var ulElement = $('<ul></ul>');
                    var liElement = $('<li class="no-checkmark"><strong>Latest Block Stats</strong></li>');
                    ulElement.append(liElement);
                    var liElement = $('<li class="no-checkmark">&nbsp;</li>');
                    ulElement.append(liElement);
                    // Iterate through the JSON object
                    for (var key in response['cardano_blocks'][0]) {
                        if (response['cardano_blocks'][0].hasOwnProperty(key)) {
                            // Create a <li> element for each key-value pair using jQuery
                            var liElement = $('<li><strong>' + key + ':</strong> ' + response['cardano_blocks'][0][key] + '</li>');
                            
                            // Append the <li> element to the <ul> using jQuery
                            ulElement.append(liElement);
                        }
                    }

                    // Update the content of the div with the generated <ul> using jQuery
                    blockchainElement.append(ulElement);

                } else {

                    blockchainElement.empty();

                    msg = response.errorMessage;

                    if (msg) {
                        alert({
                            content: msg
                        });
                    }
                }
            }).always(function () {
                $('#' + self.options.elementId + '_result').text(result);
            });
        }
    });

    return $.mage.testCardanoDbSyncConnection;
});
