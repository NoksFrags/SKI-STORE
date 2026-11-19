CREATE DATABASE IF NOT EXISTS `skistore`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `skistore`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `wishlist_items`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `carts`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name`     VARCHAR(120) NOT NULL,
    `email`         VARCHAR(180) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `phone`         VARCHAR(40)  DEFAULT NULL,
    `address`       VARCHAR(180) DEFAULT NULL,
    `city`          VARCHAR(100) DEFAULT NULL,
    `postal_code`   VARCHAR(20)  DEFAULT NULL,
    `country`       VARCHAR(80)  DEFAULT 'Bosnia and Herzegovina',
    `role`          ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` DATETIME DEFAULT NULL,
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(120) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon`        VARCHAR(40) NOT NULL DEFAULT 'snow',
    `sort_order`  INT NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id`       INT UNSIGNED DEFAULT NULL,
    `name`              VARCHAR(180) NOT NULL,
    `slug`              VARCHAR(200) NOT NULL,
    `brand`             VARCHAR(80) DEFAULT NULL,
    `sku`               VARCHAR(40) DEFAULT NULL,
    `short_description` VARCHAR(255) DEFAULT NULL,
    `description`       TEXT DEFAULT NULL,
    `price`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `compare_at_price`  DECIMAL(10,2) DEFAULT NULL,
    `stock`             INT NOT NULL DEFAULT 0,
    `image`             VARCHAR(255) DEFAULT NULL,
    `is_featured`       TINYINT(1) NOT NULL DEFAULT 0,
    `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_slug` (`slug`),
    KEY `idx_products_category` (`category_id`),
    KEY `idx_products_active` (`is_active`),
    KEY `idx_products_featured` (`is_featured`),
    KEY `idx_products_price` (`price`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carts` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED DEFAULT NULL,
    `session_token` VARCHAR(64) DEFAULT NULL,
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_carts_user` (`user_id`),
    KEY `idx_carts_token` (`session_token`),
    CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cart_items` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cart_id`    INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity`   INT NOT NULL DEFAULT 1,
    `added_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cart_items` (`cart_id`, `product_id`),
    KEY `idx_cart_items_product` (`product_id`),
    CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wishlist_items` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_wishlist` (`user_id`, `product_id`),
    KEY `idx_wishlist_product` (`product_id`),
    CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
    `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_number`         VARCHAR(30) NOT NULL,
    `user_id`              INT UNSIGNED DEFAULT NULL,
    `status`               ENUM('pending','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
    `payment_method`       ENUM('card','cash_on_delivery','bank_transfer') NOT NULL DEFAULT 'card',
    `subtotal`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `shipping_cost`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total`                DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `customer_name`        VARCHAR(120) NOT NULL,
    `email`                VARCHAR(180) NOT NULL,
    `phone`                VARCHAR(40) NOT NULL,
    `shipping_address`     VARCHAR(180) NOT NULL,
    `shipping_city`        VARCHAR(100) NOT NULL,
    `shipping_postal_code` VARCHAR(20) NOT NULL,
    `shipping_country`     VARCHAR(80) NOT NULL DEFAULT 'Bosnia and Herzegovina',
    `notes`                TEXT DEFAULT NULL,
    `created_at`           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_orders_number` (`order_number`),
    KEY `idx_orders_user` (`user_id`),
    KEY `idx_orders_status` (`status`),
    KEY `idx_orders_created` (`created_at`),
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`     INT UNSIGNED NOT NULL,
    `product_id`   INT UNSIGNED DEFAULT NULL,
    `product_name` VARCHAR(180) NOT NULL,
    `unit_price`   DECIMAL(10,2) NOT NULL,
    `quantity`     INT NOT NULL,
    `line_total`   DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_order_items_order` (`order_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users`
    (`id`, `full_name`, `email`, `password_hash`, `phone`, `address`, `city`, `postal_code`, `country`, `role`, `created_at`)
VALUES
    (1, 'Store Manager', 'admin@skistore.test', '$2y$10$lBt40lUqi4oSZUaUW/1aUumxIIKP.ZT9Hc/yYc/31hD2yt./h8dRi', '+387 33 555 100', 'Zmaja od Bosne 4', 'Sarajevo', '71000', 'Bosnia and Herzegovina', 'admin', DATE_SUB(NOW(), INTERVAL 240 DAY)),
    (2, 'Demo Customer', 'customer@skistore.test', '$2y$10$WkEgnISOxDQ7GCQ0lYS5w.7nzEnwDRAiBhRsg97RfiyzPTJw3kcSK', '+387 61 555 200', 'Ferhadija 12', 'Sarajevo', '71000', 'Bosnia and Herzegovina', 'customer', DATE_SUB(NOW(), INTERVAL 120 DAY));

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`) VALUES
    (1, 'Skis', 'skis', 'All-mountain, carving and touring skis built for every kind of snow.', 'ski', 1),
    (2, 'Snowboards', 'snowboards', 'Freeride, powder and park boards with a playful, stable ride.', 'snowboard', 2),
    (3, 'Ski Boots', 'ski-boots', 'Precise-fitting boots for resort days and long tours.', 'boot', 3),
    (4, 'Jackets & Pants', 'jackets-pants', 'Weatherproof shells and insulated layers for cold mountain days.', 'jacket', 4),
    (5, 'Helmets & Goggles', 'helmets-goggles', 'Protection and clear vision in every light condition.', 'helmet', 5),
    (6, 'Accessories', 'accessories', 'Poles, gloves, packs and tuning gear that complete the kit.', 'gear', 6);

INSERT INTO `products`
    (`id`, `category_id`, `name`, `slug`, `brand`, `sku`, `short_description`, `description`, `price`, `compare_at_price`, `stock`, `image`, `is_featured`, `is_active`, `created_at`)
VALUES
    (1, 1, 'Atomic Bent 100', 'atomic-bent-100', 'Atomic', 'AA0030694',
     'Playful 100 mm freeride ski. 128.5/100/119 mm sidecut and a 19 m radius at 179 cm.',
     'The most balanced ski in the Bent family: a light poplar core, HRZN 3D tips for float and a rocker-camber-rocker profile that still grips firm snow. Sizes 158, 165, 172, 179 and 186 cm. Sold flat, bindings separate.',
     1249.00, 1299.00, 9, 'assets/img/products/skis-atlas.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
    (2, 1, 'Rossignol Sender 100', 'rossignol-sender-100', 'Rossignol', 'RP2M006',
     'All-mountain freeride ski with a poplar core and a 100 mm waist.',
     'New for 2026, the Sender 100 pairs a light poplar core with an extended tip rocker and a slightly stiffer tail. Built for variable snow and riders who drive the front of the boot. Sizes 162 to 192 cm.',
     1349.00, NULL, 7, 'assets/img/products/skis-glacier.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 55 DAY)),
    (3, 1, 'Salomon QST 106', 'salomon-qst-106', 'Salomon', 'L47161200',
     'Freeride charger: 106 mm waist, C/FX carbon-flax weave, full sandwich sidewall.',
     'The widest all-mountain QST pairs a karuba-poplar core with the C/FX carbon-flax weave, keeping the ski calm at speed without metal. Tip and tail rocker with a long effective edge and an 18.7 m radius at 181 cm. Sizes 157 to 189 cm.',
     1499.00, 1599.00, 5, 'assets/img/products/skis-summit.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 40 DAY)),
    (4, 2, 'Burton Custom Camber 158', 'burton-custom-camber', 'Burton', '106881',
     'The benchmark all-mountain board: positive camber, Super Fly II 700G core, sintered base.',
     'The longest-running shape in snowboarding, still built by hand in Vermont. Positive camber underfoot for edge hold, a tapered 25 mm nose for float and the Channel mounting system. 158 cm, 250 mm waist, 7/10 flex.',
     1299.00, NULL, 6, 'assets/img/products/board-ridge.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 35 DAY)),
    (5, 2, 'Jones Mountain Twin 157', 'jones-mountain-twin', 'Jones', 'J.25.SBM.MTN.157',
     'Directional twin freestyle board with a 3D Contour Base and bamboo stringers.',
     'A freeride twin that handles park laps and powder days. Traction Tech 2.0 serrated edges bite on ice, the sintered 9900 base is fast and the 3D Contour Base keeps the nose from catching. 157 cm, 254 mm waist.',
     1150.00, NULL, 8, 'assets/img/products/board-powder.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 30 DAY)),
    (6, 2, 'Capita Paradise 149', 'capita-paradise', 'Capita', '1231149',
     'Women''s all-terrain board: Resort V3 camber, FSC-certified wood core.',
     'One board for the whole mountain. A poplar and beech core with carbon stringers, a sintered base and a medium-soft flex that presses easily in the park. 149 cm, 242 mm waist.',
     949.00, 1049.00, 10, 'assets/img/products/board-park.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 26 DAY)),
    (7, 3, 'Atomic Hawx Magna 100', 'atomic-hawx-magna-100', 'Atomic', 'AE5021200',
     'Wide-fit all-mountain boot: 100 flex, 102 mm last, heat-mouldable Memory Fit shell.',
     'Built for wider feet: a 102 mm last, a 100 flex and a Memory Fit shell that a bootfitter can heat-mould to your foot in about 20 minutes. GripWalk soles and a 35 mm power strap.',
     799.00, 899.00, 11, 'assets/img/products/boots-altitude.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 24 DAY)),
    (8, 3, 'Tecnica Mach1 110 MV', 'tecnica-mach1-110-mv', 'Tecnica', '20161G',
     '110-flex performance boot with a 100 mm mid-volume last and C.A.S. custom shell.',
     'A precise, responsive boot for strong intermediate to advanced skiers. The C.A.S. shell and liner can be customised by a fitter, the asymmetric cuff drives the ski onto edge and the four-buckle overlap closure is proven. 100 mm last.',
     1049.00, NULL, 7, 'assets/img/products/boots-crest.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 22 DAY)),
    (9, 4, 'Arc''teryx Sabre Jacket', 'arcteryx-sabre-jacket', 'Arc''teryx', 'X000007261',
     'Freeride shell: 80D GORE-TEX, helmet-compatible StormHood, pit zips.',
     'The resort-focused freeride shell from Arc''teryx. A durable 80D GORE-TEX face fabric, a helmet-compatible StormHood and a trim fit that layers over a midlayer. Two hand pockets, an internal dump pocket and RS zipper sliders.',
     1390.00, 1490.00, 6, 'assets/img/products/jacket-shell.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 20 DAY)),
    (10, 4, 'Patagonia Powder Town Jacket', 'patagonia-powder-town-jacket', 'Patagonia', '31195',
     'Insulated 2-layer ski jacket in recycled polyester with a PFC-free DWR.',
     'Warm without bulk: 60 g recycled polyester insulation, a 2-layer H2No shell and a PFC-free durable water repellent. Helmet-compatible hood, underarm vents and a powder skirt. Fair Trade Certified sewn.',
     599.00, NULL, 14, 'assets/img/products/jacket-down.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 18 DAY)),
    (11, 4, 'The North Face Freedom Insulated Pants', 'tnf-freedom-insulated-pants', 'The North Face', 'NF0A5A5N',
     'Insulated ski pant with a 2-layer DryVent shell and reinforced kick patches.',
     'The everyday resort pant: a waterproof DryVent 2L shell, 60 g Heatseeker insulation, articulated knees and Chimney Venting. Regular fit with a hem gaiter that stretches over a boot.',
     379.00, NULL, 12, 'assets/img/products/pants-ridge.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 16 DAY)),
    (12, 5, 'Smith Vantage 2 MIPS', 'smith-vantage-2-mips', 'Smith', 'E005289VX',
     'All-mountain helmet with MIPS, zonal Koroyd protection and BOA 360 fit.',
     'The flagship all-mountain helmet from Smith: a hybrid shell with zonal Koroyd energy absorption and MIPS rotational protection. Adjustable 22-vent airflow, BOA 360 fit system and audio-compatible ear pads. 400 g in size M.',
     479.00, NULL, 9, 'assets/img/products/helmet-meridian.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 14 DAY)),
    (13, 5, 'Smith Squad MAG', 'smith-squad-mag', 'Smith', 'M006872',
     'Cylindrical goggle with magnetic MAG lens swap and ChromaPop optics.',
     'Two lenses in the box, swapped in seconds with the MAG system. ChromaPop lens technology lifts contrast in flat light and the 5X anti-fog inner lens stays clear on the lift. Medium to large fit, helmet compatible.',
     449.00, NULL, 18, 'assets/img/products/goggles-vision.svg', 1, 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
    (14, 5, 'Oakley Flight Deck L', 'oakley-flight-deck-l', 'Oakley', 'OO7050-59',
     'Frameless toric lens goggle with Prizm Snow optics in a large fit.',
     'A rimless, wide-field design with Prizm lens filtering tuned for snow. Triple-layer face foam, a 40 mm adjustable strap and a prescription-friendly fit. Includes a microbag for storage.',
     329.00, NULL, 0, 'assets/img/products/goggles-storm.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (15, 6, 'Hestra Army Leather Heli Ski Glove', 'hestra-army-leather-heli-ski-glove', 'Hestra', '30570-100',
     'Leather ski glove with a removable wool liner and snow-lock cuff.',
     'A guide favourite. Impregnated army goat leather palm, a removable 100% wool liner that dries overnight and a snow-lock cuff that seals over the jacket. Hand-sewn in Sweden. Unisex sizes 6 to 11.',
     329.00, NULL, 20, 'assets/img/products/acc-gloves.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
    (16, 6, 'Dakine Heli Pro 20L', 'dakine-heli-pro-20l', 'Dakine', '10002095',
     '20 L backcountry pack with diagonal ski carry, shovel pocket and hydration sleeve.',
     'Close-fitting and stable on the descent: 20 litres, a dedicated avalanche tool pocket, diagonal or A-frame ski carry and a fleece-lined goggle pocket. 1.1 kg, hydration sleeve fits a 3 L reservoir.',
     299.00, NULL, 8, 'assets/img/products/acc-backpack.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),
    (17, 6, 'Leki Detect Vario TR XL', 'leki-detect-vario-tr-xl', 'Leki', '65322371',
     'Adjustable carbon touring pole with Trigger S grip, 110 to 130 cm.',
     'A carbon upper and aluminium lower keep weight down on the skintrack, while the Trigger S grip releases instantly without taking the strap off. Adjustable 110 to 130 cm with a 100 mm touring basket.',
     289.00, NULL, 5, 'assets/img/products/acc-poles.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (18, 6, 'Swix Waxing Kit Pro', 'swix-waxing-kit-pro', 'Swix', 'TA5000',
     'Wax and edge tuning kit: iron, three waxes, scraper, brush and edge file.',
     'Everything for a proper weekly tune: a 1000 W wax iron with a flat base, three temperature-range hydrocarbon waxes, a plexi scraper, a nylon brush and an edge file with a guide, in a zip case.',
     189.00, NULL, 12, 'assets/img/products/acc-tuning.svg', 0, 1, DATE_SUB(NOW(), INTERVAL 4 DAY));
INSERT INTO `orders`
    (`id`, `order_number`, `user_id`, `status`, `payment_method`, `subtotal`, `shipping_cost`, `total`, `customer_name`, `email`, `phone`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `shipping_country`, `notes`, `created_at`)
VALUES
    (1, 'SK-240601-4A7C', 2, 'completed', 'card', 747.00, 0.00, 747.00, 'Demo Customer', 'customer@skistore.test', '+387 61 555 200', 'Ferhadija 12', 'Sarajevo', '71000', 'Bosnia and Herzegovina', NULL, DATE_SUB(NOW(), INTERVAL 150 DAY)),
    (2, 'SK-240901-9B2E', 2, 'shipped', 'cash_on_delivery', 1118.00, 0.00, 1118.00, 'Demo Customer', 'customer@skistore.test', '+387 61 555 200', 'Ferhadija 12', 'Sarajevo', '71000', 'Bosnia and Herzegovina', 'Leave at the reception please.', DATE_SUB(NOW(), INTERVAL 45 DAY)),
    (3, 'SK-241101-1C6F', 2, 'pending', 'bank_transfer', 228.00, 0.00, 228.00, 'Demo Customer', 'customer@skistore.test', '+387 61 555 200', 'Ferhadija 12', 'Sarajevo', '71000', 'Bosnia and Herzegovina', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY));

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `line_total`) VALUES
    (1, 7,  'Altitude 120 Ski Boots', 549.00, 1, 549.00),
    (1, 16, 'Guide Gloves', 99.00, 2, 198.00),
    (2, 1,  'Atlas All-Mountain Skis', 899.00, 1, 899.00),
    (2, 12, 'Meridian MIPS Helmet', 219.00, 1, 219.00),
    (3, 13, 'Vision Pro Goggles', 149.00, 1, 149.00),
    (3, 18, 'Tuning Kit Pro', 79.00, 1, 79.00);

INSERT INTO `wishlist_items` (`user_id`, `product_id`) VALUES
    (2, 3),
    (2, 9);
