<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240802012938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert the tables to use more standard conventions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE AccessLog
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN IP TO ip
                ,   RENAME COLUMN Username TO username
                ,   RENAME COLUMN Time TO timestamp
                ,   RENAME COLUMN URI TO uri
                ,   RENAME COLUMN Title TO title
                ,   RENAME COLUMN Referer TO referer
                ,   RENAME COLUMN RefererTitle TO referer_title
                ,   RENAME COLUMN Browser TO browser
                ,   RENAME TO access_log
        ');

        $this->addSql('
            ALTER TABLE CartItems
                    RENAME COLUMN ID TO id
                ,   ADD COLUMN user_id INT UNSIGNED NOT NULL AFTER id
                ,   RENAME COLUMN ItemID TO item_id
                ,   RENAME COLUMN Quantity TO quantity
                ,   RENAME INDEX ItemID TO item_id
                ,   DROP INDEX UserItem
                ,   DROP FOREIGN KEY CartItems_ibfk_2
                ,   RENAME TO cart_item
        ');
        $this->addSql('
            UPDATE cart_item
            INNER JOIN user ON user.username_canonical = cart_item.Username
            SET cart_item.user_id = user.id
        ');
        $this->addSql('
            ALTER TABLE cart_item
                    DROP COLUMN Username
                ,   ADD UNIQUE INDEX user_item (user_id, item_id)
        ');

        $this->addSql('
            ALTER TABLE CategoryClosure
                    RENAME COLUMN Parent TO parent
                ,   RENAME COLUMN Child TO child
                ,   RENAME COLUMN Depth TO depth
                ,   RENAME INDEX PDC TO pdc
                ,   RENAME INDEX CPD TO cpd
                ,   RENAME TO category_closure
        ');

        $this->addSql('
            ALTER TABLE CategoryHierarchy
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN Sets TO sets
                ,   RENAME INDEX Sets TO sets
                ,   RENAME TO category_hierarchy
        ');

        $this->addSql('
            ALTER TABLE CategoryInfo
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN Parent TO parent
                ,   RENAME COLUMN Name TO name
                ,   RENAME COLUMN Slug TO slug
                ,   RENAME COLUMN Description TO description
                ,   RENAME COLUMN IsViewable TO is_viewable
                ,   RENAME COLUMN LastModified TO modified_at
                ,   RENAME INDEX Parent TO parent
                ,   RENAME TO category
        ');

        $this->addSql('
            ALTER TABLE CostModifiers
                    RENAME COLUMN ManufacturerID TO manufacturer_id
                ,   RENAME COLUMN Modifier TO modifier
                ,   RENAME TO cost_modifier
        ');

        $this->addSql('
            ALTER TABLE CustomerInfo
                    ADD COLUMN user_id INT UNSIGNED NOT NULL FIRST
                ,   RENAME COLUMN BillName TO bill_name
                ,   RENAME COLUMN BillCompany TO bill_company
                ,   RENAME COLUMN BillAddress1 TO bill_address1
                ,   RENAME COLUMN BillAddress2 TO bill_address2
                ,   RENAME COLUMN BillCity TO bill_city
                ,   RENAME COLUMN BillState TO bill_state
                ,   RENAME COLUMN BillZip TO bill_zip_code
                ,   RENAME COLUMN BillCountry TO bill_country
                ,   RENAME COLUMN ShipName TO ship_name
                ,   RENAME COLUMN ShipCompany TO ship_company
                ,   RENAME COLUMN ShipAddress1 TO ship_address1
                ,   RENAME COLUMN ShipAddress2 TO ship_address2
                ,   RENAME COLUMN ShipCity TO ship_city
                ,   RENAME COLUMN ShipState TO ship_state
                ,   RENAME COLUMN ShipZip TO ship_zip_code
                ,   RENAME COLUMN ShipCountry TO ship_country
                ,   RENAME COLUMN PhoneNumber TO phone_number
                ,   RENAME COLUMN EmailAddress TO email
                ,   DROP INDEX Username
                ,   DROP FOREIGN KEY CustomerInfo_ibfk_1
                ,   RENAME TO customer
        ');
        $this->addSql('
            UPDATE customer
            INNER JOIN User ON User.username_canonical = customer.Username
            SET customer.user_id = User.id
        ');
        $this->addSql('
            ALTER TABLE customer
                    DROP COLUMN Username
                ,   ADD UNIQUE INDEX user_id (user_id)
        ');

        $this->addSql('
            ALTER TABLE ImageInfo
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN Image TO image
                ,   RENAME COLUMN ImageHash TO image_hash
                ,   RENAME COLUMN Title TO title
                ,   RENAME COLUMN Width TO width
                ,   RENAME COLUMN Height TO height
                ,   RENAME COLUMN ThumbWidth TO thumb_width
                ,   RENAME COLUMN ThumbHeight TO thumb_height
                ,   RENAME COLUMN DateAdded TO added_at
                ,   RENAME INDEX ImageHash TO image_hash
                ,   RENAME INDEX Image TO image
                ,   RENAME TO image
        ');

        $this->addSql('
            ALTER TABLE ItemAvailability
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN Availability TO availability
                ,   RENAME TO availability
        ');

        $this->addSql('
            ALTER TABLE ItemCategories
                    RENAME COLUMN ItemID TO item_id
                ,   RENAME COLUMN CategoryID TO category_id
                ,   RENAME INDEX CategoryID TO category_id
                ,   ADD UNIQUE INDEX item_category (item_id, category_id)
                ,   RENAME TO item_category
        ');

        $this->addSql('
            ALTER TABLE ItemImages
                    RENAME COLUMN ItemID TO item_id
                ,   RENAME COLUMN ImageID TO image_id
                ,   RENAME COLUMN Position TO position
                ,   RENAME INDEX ItemID TO item_image
                ,   RENAME INDEX ImageID TO image_id
                ,   RENAME TO item_image
        ');

        $this->addSql('
            ALTER TABLE ItemInfo
                    RENAME COLUMN ItemID TO id
                ,   RENAME COLUMN Name TO name
                ,   RENAME COLUMN Slug TO slug
                ,   RENAME COLUMN SKU TO sku
                ,   RENAME COLUMN Description TO description
                ,   RENAME COLUMN ManufacturerID TO manufacturer_id
                ,   RENAME COLUMN Cost TO cost_decimal
                ,   ADD COLUMN cost INT UNSIGNED NOT NULL DEFAULT 0 AFTER cost_decimal
                ,   RENAME COLUMN Quantity TO quantity
                ,   RENAME COLUMN Availability TO availability_id
                ,   RENAME COLUMN Weight TO weight
                ,   RENAME COLUMN Length TO length
                ,   RENAME COLUMN Width TO width
                ,   RENAME COLUMN Height TO height
                ,   RENAME COLUMN IsProduct TO is_product
                ,   RENAME COLUMN IsViewable TO is_viewable
                ,   RENAME COLUMN IsPurchasable TO is_purchasable
                ,   RENAME COLUMN IsSpecial TO is_special
                ,   RENAME COLUMN IsNew TO is_new
                ,   RENAME COLUMN ChargeTax TO charge_tax
                ,   RENAME COLUMN ChargeShipping TO charge_shipping
                ,   RENAME COLUMN FreeShipping TO is_free_shipping
                ,   RENAME COLUMN FreightQuoteReq TO freight_quote_required
                ,   RENAME COLUMN LastModified TO modified_at
                ,   RENAME INDEX ManufacturerID TO manufacturer_id
                ,   RENAME INDEX IsProduct TO is_product
                ,   RENAME INDEX IsViewable TO is_viewable
                ,   RENAME INDEX IsPurchasable TO is_purchasable
                ,   RENAME INDEX IsSpecial TO is_special
                ,   RENAME INDEX IsNew TO is_new
                ,   RENAME INDEX ChargeShipping TO charge_shipping
                ,   RENAME INDEX FreeShipping TO is_free_shipping
                ,   RENAME TO item
        ');
        $this->addSql('UPDATE item SET cost = FLOOR(cost_decimal * 100)');
        $this->addSql('ALTER TABLE item DROP COLUMN cost_decimal');

        $this->addSql('
            ALTER TABLE ItemManufacturers
                    RENAME COLUMN ID TO id
                ,   CHANGE COLUMN Name name VARCHAR(64) NOT NULL
                ,   RENAME INDEX Name TO name
                ,   RENAME TO manufacturer
        ');

        $this->addSql('
            ALTER TABLE OrderInfo
                    RENAME COLUMN OrderID TO id
                ,   RENAME COLUMN Username TO username
                ,   RENAME COLUMN OrderNumber TO order_number
                ,   RENAME COLUMN ReceiptID TO receipt_id
                ,   RENAME COLUMN BillName TO bill_name
                ,   RENAME COLUMN BillCompany TO bill_company
                ,   RENAME COLUMN BillAddress1 TO bill_address_1
                ,   RENAME COLUMN BillAddress2 TO bill_address_2
                ,   RENAME COLUMN BillCity TO bill_city
                ,   RENAME COLUMN BillState TO bill_state
                ,   RENAME COLUMN BillZip TO bill_zip_code
                ,   RENAME COLUMN BillCountry TO bill_country
                ,   RENAME COLUMN ShipName TO ship_name
                ,   RENAME COLUMN ShipCompany TO ship_company
                ,   RENAME COLUMN ShipAddress1 TO ship_address_1
                ,   RENAME COLUMN ShipAddress2 TO ship_address_2
                ,   RENAME COLUMN ShipCity TO ship_city
                ,   RENAME COLUMN ShipState TO ship_state
                ,   RENAME COLUMN ShipZip TO ship_zip_code
                ,   RENAME COLUMN ShipCountry TO ship_country
                ,   RENAME COLUMN PhoneNumber TO phone_number
                ,   RENAME COLUMN EmailAddress TO email
                ,   RENAME COLUMN Comments TO comments
                ,   RENAME COLUMN Subtotal TO subtotal_decimal
                ,   ADD COLUMN subtotal INT UNSIGNED NOT NULL AFTER subtotal_decimal
                ,   RENAME COLUMN Tax TO tax_decimal
                ,   ADD COLUMN tax INT UNSIGNED NOT NULL AFTER tax_decimal
                ,   RENAME COLUMN Shipping TO shipping_decimal
                ,   ADD COLUMN shipping INT UNSIGNED NOT NULL AFTER shipping_decimal
                ,   RENAME COLUMN RefundUnusedShipping TO refund_unused_shipping
                ,   RENAME COLUMN CreditCard TO credit_card
                ,   RENAME COLUMN Date TO created_at
                ,   RENAME INDEX Username TO username
                ,   RENAME INDEX OrderNumber TO order_number
                ,   RENAME INDEX Date TO created_at
                ,   RENAME TO order_info
        ');
        $this->addSql('
            UPDATE order_info SET
                    subtotal = FLOOR(subtotal_decimal * 100)
                ,   tax = FLOOR(tax_decimal * 100)
                ,   shipping = FLOOR(shipping_decimal * 100)
        ');
        $this->addSql('
            ALTER TABLE order_info
                    DROP COLUMN subtotal_decimal
                ,   DROP COLUMN tax_decimal
                ,   DROP COLUMN shipping_decimal
        ');

        $this->addSql('
            ALTER TABLE OrderItems
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN OrderID TO order_id
                ,   RENAME COLUMN Quantity TO quantity
                ,   RENAME COLUMN Name TO name
                ,   RENAME COLUMN SKU TO sku
                ,   RENAME COLUMN Cost TO cost_decimal
                ,   ADD COLUMN cost INT UNSIGNED NOT NULL AFTER cost_decimal
                ,   RENAME INDEX OrderID TO order_id
                ,   RENAME TO order_item
        ');
        $this->addSql('UPDATE order_item SET cost = FLOOR(cost_decimal * 100)');
        $this->addSql('ALTER TABLE order_item DROP COLUMN cost_decimal');

        $this->addSql('
            ALTER TABLE PageContent
                    RENAME COLUMN Page TO page
                ,   RENAME COLUMN Title TO title
                ,   RENAME COLUMN Content TO content
                ,   RENAME COLUMN LastModified TO modified_at
                ,   RENAME TO page_content
        ');

        $this->addSql('
            ALTER TABLE Sessions
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN Data TO data
                ,   ADD COLUMN created_at INT UNSIGNED NOT NULL AFTER data
                ,   RENAME COLUMN Lifetime TO lifetime
                ,   RENAME TO sf_session
        ');
        $this->addSql('UPDATE sf_session SET created_at = LastActivity');
        $this->addSql('ALTER TABLE sf_session DROP COLUMN LastActivity');

        $this->addSql('
            ALTER TABLE TaxRates
                    RENAME COLUMN State TO state
                ,   RENAME COLUMN Rate TO rate
                ,   RENAME INDEX State TO state
                ,   RENAME TO tax_rate
        ');

        $this->addSql('
            ALTER TABLE TransactionLog
                    RENAME COLUMN ID TO id
                ,   RENAME COLUMN OrderID TO order_id
                ,   RENAME COLUMN ReferencedID TO referenced_id
                ,   RENAME COLUMN TransactionID TO transaction_id
                ,   RENAME COLUMN Action TO action
                ,   RENAME COLUMN Amount TO amount_decimal
                ,   ADD COLUMN amount INT UNSIGNED NOT NULL AFTER amount_decimal
                ,   RENAME COLUMN Success TO is_success
                ,   RENAME COLUMN AVSSuccess TO is_avs_success
                ,   RENAME COLUMN CVV2Success TO is_cvv2_success
                ,   RENAME COLUMN Date TO created_at
                ,   RENAME INDEX TransactionID TO transaction_id
                ,   RENAME INDEX OrderID TO order_id
                ,   RENAME TO transaction_log
        ');
        $this->addSql('UPDATE transaction_log SET amount = FLOOR(amount_decimal * 100)');
        $this->addSql('ALTER TABLE transaction_log DROP COLUMN amount_decimal');

        $this->addSql('
            ALTER TABLE User
                    RENAME COLUMN password TO password_binary
                ,   ADD COLUMN password VARCHAR(255) NOT NULL AFTER password_binary
                ,   RENAME COLUMN roles TO roles_serialized
                ,   ADD COLUMN roles JSON NOT NULL AFTER roles_serialized
                ,   RENAME TO user_tmp
        ');
        $this->addSql('ALTER TABLE user_tmp RENAME TO user');
        // NOTE: This ugly hack for dealing with roles leverages the fact that
        // the database only contains users with a single role.
        $this->addSql('
            UPDATE user SET
                    password = CAST(password_binary AS CHAR)
                ,   roles = JSON_ARRAY(SUBSTRING_INDEX(SUBSTRING_INDEX(roles_serialized, \'"\', 2), \'"\', -1))
        ');
        $this->addSql('
            ALTER TABLE user
                    DROP COLUMN password_binary
                ,   DROP COLUMN roles_serialized
        ');

        $this->addSql('
            ALTER TABLE cart_item
                    DROP FOREIGN KEY CartItems_ibfk_1
                ,   ADD CONSTRAINT cart_item_ibfk_1 FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE
                ,   ADD CONSTRAINT cart_item_ibfk_2 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
        ');

        $this->addSql('ALTER TABLE customer ADD CONSTRAINT customer_ibfk_1 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE');

        $this->addSql('
            ALTER TABLE item_category
                    DROP FOREIGN KEY ItemCategories_ibfk_1
                ,   ADD CONSTRAINT item_category_ibfk_1 FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE item_image
                    DROP FOREIGN KEY ItemImages_ibfk_1
                ,   DROP FOREIGN KEY ItemImages_ibfk_2
                ,   ADD CONSTRAINT item_image_ibfk_1 FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE
                ,   ADD CONSTRAINT item_image_ibfk_2 FOREIGN KEY (image_id) REFERENCES image(id)
        ');

        $this->addSql('
            ALTER TABLE order_item
                    DROP FOREIGN KEY OrderItems_ibfk_1
                ,   ADD CONSTRAINT order_item_ibfk_1 FOREIGN KEY (order_id) REFERENCES order_info(id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_item ADD COLUMN Username VARCHAR(255) NOT NULL AFTER user_id');
        $this->addSql('
            ALTER TABLE cart_item
                    DROP FOREIGN KEY cart_item_ibfk_1
                ,   DROP FOREIGN KEY cart_item_ibfk_2
                ,   ADD CONSTRAINT CartItems_ibfk_1 FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE customer
                    ADD COLUMN Username VARCHAR(255) NOT NULL AFTER user_id
                ,   DROP FOREIGN KEY customer_ibfk_1
        ');

        $this->addSql('
            ALTER TABLE item_category
                    DROP FOREIGN KEY item_category_ibfk_1
                ,   ADD CONSTRAINT ItemCategories_ibfk_1 FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE item_image
                    DROP FOREIGN KEY item_image_ibfk_1
                ,   DROP FOREIGN KEY item_image_ibfk_2
                ,   ADD CONSTRAINT ItemImages_ibfk_1 FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE
                ,   ADD CONSTRAINT ItemImages_ibfk_2 FOREIGN KEY (image_id) REFERENCES image(id)
        ');

        $this->addSql('
            ALTER TABLE order_item
                    DROP FOREIGN KEY order_item_ibfk_1
                ,   ADD CONSTRAINT OrderItems_ibfk_1 FOREIGN KEY (order_id) REFERENCES order_info(id) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE access_log
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN ip TO IP
                ,   RENAME COLUMN username TO Username
                ,   RENAME COLUMN timestamp TO Time
                ,   RENAME COLUMN uri TO URI
                ,   RENAME COLUMN title TO Title
                ,   RENAME COLUMN referer TO Referer
                ,   RENAME COLUMN referer_title TO RefererTitle
                ,   RENAME COLUMN browser TO Browser
                ,   RENAME TO AccessLog
        ');

        $this->addSql('
            ALTER TABLE availability
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN availability TO Availability
                ,   RENAME TO ItemAvailability
        ');

        $this->addSql('
            ALTER TABLE cart_item
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN item_id TO ItemID
                ,   RENAME COLUMN quantity TO Quantity
                ,   RENAME INDEX item_id TO ItemID
                ,   DROP INDEX user_item
                ,   RENAME TO CartItems
        ');
        $this->addSql('
            UPDATE CartItems
            INNER JOIN user ON user.id = CartItems.user_id
            SET CartItems.Username = user.username_canonical
        ');
        $this->addSql('
            ALTER TABLE CartItems
                    DROP COLUMN user_id
                ,   ADD UNIQUE INDEX UserItem (Username, ItemID)
                ,   ADD CONSTRAINT CartItems_ibfk_2 FOREIGN KEY (Username) REFERENCES user(username_canonical) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE category_closure
                    RENAME COLUMN parent TO Parent
                ,   RENAME COLUMN child TO Child
                ,   RENAME COLUMN depth TO Depth
                ,   RENAME INDEX pdc TO PDC
                ,   RENAME INDEX cpd TO CPD
                ,   RENAME TO CategoryClosure
        ');

        $this->addSql('
            ALTER TABLE category_hierarchy
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN sets TO Sets
                ,   RENAME INDEX sets TO Sets
                ,   RENAME TO CategoryHierarchy
        ');

        $this->addSql('
            ALTER TABLE category
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN parent TO Parent
                ,   RENAME COLUMN name TO Name
                ,   RENAME COLUMN slug TO Slug
                ,   RENAME COLUMN description TO Description
                ,   RENAME COLUMN is_viewable TO IsViewable
                ,   RENAME COLUMN modified_at TO LastModified
                ,   RENAME INDEX parent TO Parent
                ,   RENAME TO CategoryInfo
        ');

        $this->addSql('
            ALTER TABLE cost_modifier
                    RENAME COLUMN manufacturer_id TO ManufacturerID
                ,   RENAME COLUMN modifier TO Modifier
                ,   RENAME TO CostModifiers
        ');

        $this->addSql('
            ALTER TABLE customer
                    RENAME COLUMN bill_name TO BillName
                ,   RENAME COLUMN bill_company TO BillCompany
                ,   RENAME COLUMN bill_address1 TO BillAddress1
                ,   RENAME COLUMN bill_address2 TO BillAddress2
                ,   RENAME COLUMN bill_city TO BillCity
                ,   RENAME COLUMN bill_state TO BillState
                ,   RENAME COLUMN bill_zip_code TO BillZip
                ,   RENAME COLUMN bill_country TO BillCountry
                ,   RENAME COLUMN ship_name TO ShipName
                ,   RENAME COLUMN ship_company TO ShipCompany
                ,   RENAME COLUMN ship_address1 TO ShipAddress1
                ,   RENAME COLUMN ship_address2 TO ShipAddress2
                ,   RENAME COLUMN ship_city TO ShipCity
                ,   RENAME COLUMN ship_state TO ShipState
                ,   RENAME COLUMN ship_zip_code TO ShipZip
                ,   RENAME COLUMN ship_country TO ShipCountry
                ,   RENAME COLUMN phone_number TO PhoneNumber
                ,   RENAME COLUMN email TO EmailAddress
                ,   DROP INDEX user_id
                ,   RENAME TO CustomerInfo
        ');
        $this->addSql('
            UPDATE CustomerInfo
            INNER JOIN user ON user.id = CustomerInfo.user_id
            SET CustomerInfo.Username = user.username_canonical
        ');
        $this->addSql('
            ALTER TABLE CustomerInfo
                    DROP COLUMN user_id
                ,   ADD UNIQUE INDEX Username (Username)
                ,   ADD CONSTRAINT CustomerInfo_ibfk_1 FOREIGN KEY (Username) REFERENCES user(username_canonical) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE image
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN image TO Image
                ,   RENAME COLUMN image_hash TO ImageHash
                ,   RENAME COLUMN title TO Title
                ,   RENAME COLUMN width TO Width
                ,   RENAME COLUMN height TO Height
                ,   RENAME COLUMN thumb_width TO ThumbWidth
                ,   RENAME COLUMN thumb_height TO ThumbHeight
                ,   RENAME COLUMN added_at TO DateAdded
                ,   RENAME INDEX image_hash TO ImageHash
                ,   RENAME INDEX image TO Image
                ,   RENAME TO ImageInfo
        ');

        $this->addSql('
            ALTER TABLE item_category
                    RENAME COLUMN item_id TO ItemID
                ,   RENAME COLUMN category_id TO CategoryID
                ,   RENAME INDEX category_id TO CategoryID
                ,   DROP INDEX item_category
                ,   RENAME TO ItemCategories
        ');

        $this->addSql('
            ALTER TABLE item_image
                    RENAME COLUMN item_id TO ItemID
                ,   RENAME COLUMN image_id TO ImageID
                ,   RENAME COLUMN position TO Position
                ,   RENAME INDEX item_image TO ItemID
                ,   RENAME INDEX image_id TO ImageID
                ,   RENAME TO ItemImages
        ');

        $this->addSql('
            ALTER TABLE item
                    RENAME COLUMN id TO ItemID
                ,   RENAME COLUMN name TO Name
                ,   RENAME COLUMN slug TO Slug
                ,   RENAME COLUMN sku TO SKU
                ,   RENAME COLUMN description TO Description
                ,   RENAME COLUMN manufacturer_id TO ManufacturerID
                ,   RENAME COLUMN cost TO cost_cents
                ,   ADD COLUMN Cost DECIMAL(9,2) NOT NULL DEFAULT 0.00 AFTER cost_cents
                ,   RENAME COLUMN quantity TO Quantity
                ,   RENAME COLUMN availability_id TO Availability
                ,   RENAME COLUMN weight TO Weight
                ,   RENAME COLUMN length TO Length
                ,   RENAME COLUMN width TO Width
                ,   RENAME COLUMN height TO Height
                ,   RENAME COLUMN is_product TO IsProduct
                ,   RENAME COLUMN is_viewable TO IsViewable
                ,   RENAME COLUMN is_purchasable TO IsPurchasable
                ,   RENAME COLUMN is_special TO IsSpecial
                ,   RENAME COLUMN is_new TO IsNew
                ,   RENAME COLUMN charge_tax TO ChargeTax
                ,   RENAME COLUMN charge_shipping TO ChargeShipping
                ,   RENAME COLUMN is_free_shipping TO FreeShipping
                ,   RENAME COLUMN freight_quote_required TO FreightQuoteReq
                ,   RENAME COLUMN modified_at TO LastModified
                ,   RENAME INDEX manufacturer_id TO ManufacturerID
                ,   RENAME INDEX is_product TO IsProduct
                ,   RENAME INDEX is_viewable TO IsViewable
                ,   RENAME INDEX is_purchasable TO IsPurchasable
                ,   RENAME INDEX is_special TO IsSpecial
                ,   RENAME INDEX is_new TO IsNew
                ,   RENAME INDEX charge_shipping TO ChargeShipping
                ,   RENAME INDEX is_free_shipping TO FreeShipping
                ,   RENAME TO ItemInfo
        ');
        $this->addSql('UPDATE ItemInfo SET Cost = ROUND(cost_cents / 100, 2)');
        $this->addSql('ALTER TABLE ItemInfo DROP COLUMN cost_cents');

        $this->addSql('
            ALTER TABLE manufacturer
                    RENAME COLUMN id TO ID
                ,   CHANGE COLUMN name Name VARCHAR(64) DEFAULT NULL
                ,   RENAME INDEX name TO Name
                ,   RENAME TO ItemManufacturers
        ');

        $this->addSql('
            ALTER TABLE order_info
                    RENAME COLUMN id TO OrderID
                ,   RENAME COLUMN username TO Username
                ,   RENAME COLUMN order_number TO OrderNumber
                ,   RENAME COLUMN receipt_id TO ReceiptID
                ,   RENAME COLUMN bill_name TO BillName
                ,   RENAME COLUMN bill_company TO BillCompany
                ,   RENAME COLUMN bill_address_1 TO BillAddress1
                ,   RENAME COLUMN bill_address_2 TO BillAddress2
                ,   RENAME COLUMN bill_city TO BillCity
                ,   RENAME COLUMN bill_state TO BillState
                ,   RENAME COLUMN bill_zip_code TO BillZip
                ,   RENAME COLUMN bill_country TO BillCountry
                ,   RENAME COLUMN ship_name TO ShipName
                ,   RENAME COLUMN ship_company TO ShipCompany
                ,   RENAME COLUMN ship_address_1 TO ShipAddress1
                ,   RENAME COLUMN ship_address_2 TO ShipAddress2
                ,   RENAME COLUMN ship_city TO ShipCity
                ,   RENAME COLUMN ship_state TO ShipState
                ,   RENAME COLUMN ship_zip_code TO ShipZip
                ,   RENAME COLUMN ship_country TO ShipCountry
                ,   RENAME COLUMN phone_number TO PhoneNumber
                ,   RENAME COLUMN email TO EmailAddress
                ,   RENAME COLUMN comments TO comments
                ,   RENAME COLUMN subtotal TO subtotal_cents
                ,   ADD COLUMN Subtotal DECIMAL(9,2) NOT NULL AFTER subtotal_cents
                ,   RENAME COLUMN tax TO tax_cents
                ,   ADD COLUMN Tax DECIMAL(9,2) NOT NULL AFTER tax_cents
                ,   RENAME COLUMN shipping TO shipping_cents
                ,   ADD COLUMN Shipping DECIMAL(9,2) NOT NULL AFTER shipping_cents
                ,   RENAME COLUMN refund_unused_shipping TO RefundUnusedShipping
                ,   RENAME COLUMN credit_card TO CreditCard
                ,   RENAME COLUMN created_at TO Date
                ,   RENAME INDEX username TO Username
                ,   RENAME INDEX order_number TO OrderNumber
                ,   RENAME INDEX created_at TO Date
                ,   RENAME TO OrderInfo
        ');
        $this->addSql('
            UPDATE OrderInfo SET
                    Subtotal = ROUND(subtotal_cents / 100, 2)
                ,   Tax = ROUND(tax_cents / 100, 2)
                ,   Shipping = ROUND(shipping_cents / 100, 2)
        ');
        $this->addSql('
            ALTER TABLE OrderInfo
                    DROP COLUMN subtotal_cents
                ,   DROP COLUMN tax_cents
                ,   DROP COLUMN shipping_cents
        ');

        $this->addSql('
            ALTER TABLE order_item
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN order_id TO OrderID
                ,   RENAME COLUMN quantity TO Quantity
                ,   RENAME COLUMN name TO Name
                ,   RENAME COLUMN sku TO SKU
                ,   RENAME COLUMN cost TO cost_cents
                ,   ADD COLUMN Cost DECIMAL(9,2) NOT NULL AFTER cost_cents
                ,   RENAME INDEX order_id TO OrderID
                ,   RENAME TO OrderItems
        ');
        $this->addSql('UPDATE OrderItems SET Cost = ROUND(cost_cents / 100, 2)');
        $this->addSql('ALTER TABLE OrderItems DROP COLUMN cost_cents');

        $this->addSql('
            ALTER TABLE page_content
                    RENAME COLUMN page TO Page
                ,   RENAME COLUMN title TO Title
                ,   RENAME COLUMN content TO Content
                ,   RENAME COLUMN modified_at TO LastModified
                ,   RENAME TO PageContent
        ');

        $this->addSql('
            ALTER TABLE sf_session
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN data TO Data
                ,   RENAME COLUMN lifetime TO Lifetime
                ,   ADD COLUMN LastActivity INT NOT NULL AFTER Lifetime
                ,   RENAME TO Sessions
        ');
        $this->addSql('UPDATE Sessions SET LastActivity = created_at');
        $this->addSql('ALTER TABLE Sessions DROP COLUMN created_at');

        $this->addSql('
            ALTER TABLE tax_rate
                    RENAME COLUMN state TO State
                ,   RENAME COLUMN rate TO Rate
                ,   RENAME INDEX state TO State
                ,   RENAME TO TaxRates
        ');

        $this->addSql('
            ALTER TABLE transaction_log
                    RENAME COLUMN id TO ID
                ,   RENAME COLUMN order_id TO OrderID
                ,   RENAME COLUMN referenced_id TO ReferencedID
                ,   RENAME COLUMN transaction_id TO TransactionID
                ,   RENAME COLUMN action TO Action
                ,   RENAME COLUMN amount TO amount_cents
                ,   ADD COLUMN Amount DECIMAL(9,2) NOT NULL AFTER amount_cents
                ,   RENAME COLUMN is_success TO Success
                ,   RENAME COLUMN is_avs_success TO AVSSuccess
                ,   RENAME COLUMN is_cvv2_success TO CVV2Success
                ,   RENAME COLUMN created_at TO Date
                ,   RENAME INDEX transaction_id TO TransactionID
                ,   RENAME INDEX order_id TO OrderID
                ,   RENAME TO TransactionLog
        ');
        $this->addSql('UPDATE TransactionLog SET Amount = ROUND(amount_cents / 100, 2)');
        $this->addSql('ALTER TABLE TransactionLog DROP COLUMN amount_cents');

        $this->addSql('
            ALTER TABLE user
                    RENAME COLUMN password TO password_char
                ,   ADD COLUMN password VARBINARY(128) NOT NULL AFTER password_char
                ,   RENAME COLUMN roles TO roles_json
                ,   ADD COLUMN roles LONGTEXT NOT NULL AFTER roles_json
                ,   RENAME TO user_tmp
        ');
        $this->addSql('ALTER TABLE user_tmp RENAME TO User');
        // NOTE: This ugly hack for dealing with roles leverages the fact that
        // the database only contains users with a single role.
        $this->addSql('
            UPDATE User SET
                    password = CAST(password_char AS BINARY)
                ,   roles = CONCAT(\'a:1:{i:0;s:\', CHAR_LENGTH(JSON_EXTRACT(roles_json, \'$[0]\')) - 2, \':\', JSON_EXTRACT(roles_json, \'$[0]\'), \';}\')
        ');
        $this->addSql('
            ALTER TABLE User
                    DROP COLUMN password_char
                ,   DROP COLUMN roles_json
        ');
    }
}
