# KProject.pro ShareASale

This **unofficial** ShareASale Magento plugin is designed to provide a communication link between magento and ShareASale affiliate API.

As before, the new transaction recorder is the small image pixel code printed on the order success page. In addition to simplifying the integration process of ShareASale, the main reason for this project is to help with refunds/voiding of transactions using Magento observers. Moreover, the module also allows external plugins to call ShareASale New Transaction API when importing orders.

## Installation
You can either install the extension via the [Magento Connect][Connect], directly from github or use composer.

## Contribution
If you have any code to contribute or bugs to report, you can always use Github to do so. If you are creating a pull request, please describe what you are trying to fix.

## Configuration
- **Enabled** - enable when you are ready to go. This is enough to disable the module fully.
- **Merchant ID** - you can find this in your intro email and in other places for the ShareASale account.
- **Token / Secret Key** - as noted, this is found under Tools > Merchant API of your ShareASale account.
- The API (New Transaction) is a special use case where you enable the New Transactions to be made with the API as well. Which will allow 3rd party modules to pass parameters to the API before *"sales_order_place_after"* event is picked up by our module. At the moment this used by [Shopgate][SG] plugin only.

![KProject ShareASale configuration](http://kproject.pro/assets/KProject_SAS_config.png)

### Todos
 - Write Tests
 - Create a backend page for attempted API queries
 - Allow manual re-send to API from the previously mentioned page
 - Test database cleanup cron

License
----

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see the license tag below.


[//]: #
   [SG]: <https://www.magentocommerce.com/magento-connect/shopgate-the-leading-mobile-commerce-platform.html>
   [Connect]: <https://www.magentocommerce.com/magento-connect/kproject-shareasale.html>
