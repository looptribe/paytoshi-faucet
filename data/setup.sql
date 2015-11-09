CREATE TABLE IF NOT EXISTS paytoshi_settings (
    `name` varchar(64) not null,
    `value` TEXT CHARACTER SET utf8 NOT NULL,
    primary key(`name`)
);

INSERT IGNORE INTO paytoshi_settings (name, value) VALUES
    ('name', 'Paytoshi Faucet'),
    ('description', '... faucet? Paytoshi!'),
    ('api_key', ''),
    ('password', ''),
    ('installed_at', ''),
    ('rewards', '100*75.25,200*15,300'),
    ('waiting_interval', '3600'),
    ('referral_percentage', '30'),
    ('captcha_provider', 'recaptcha'),
    ('solve_media_challenge_key', ''),
    ('solve_media_verification_key', ''),
    ('solve_media_authentication_key', ''),
    ('recaptcha_private_key', ''),
    ('recaptcha_public_key', ''),
    ('funcaptcha_public_key', ''),
    ('funcaptcha_private_key', ''),
    ('theme', :theme),
    ('version', 1),
    ('custom_css', '/**
* Paytoshi Faucet Custom CSS
* 
* Add your custom CSS here
*/

body {

}'),
    ('content_header_box', '<!-- Header Content here -->
<p>HEADER</p>'),
    ('content_left_box', '<!-- Left Content here -->
<p>LEFT</p>'),
    ('content_right_box', '<!-- Right Content here -->
<h3>Paytoshi Faucets</h3>
<ul class="faucets"> 
    <li><a href="http://mariofaucet.com/">Mario Faucet</a></li>
    <li><a href="http://pulpfaucet.com/">Pulp Faucet</a></li>
    <li><a href="http://shinobifaucet.com/">Shinobi Faucet</a></li>
</ul>'),
    ('content_center1_box', '<!-- Center1 Content here -->
<p>CENTER1</p>'),
    ('content_center2_box', '<!-- Center2 Content here -->
<p>CENTER2</p>'),
    ('content_center3_box', '<!-- Center3 Content here -->
<p>CENTER3</p>'),
    ('content_footer_box', '<!-- Footer Content here -->
<div>Want more free bitcoins? Check out <a href="https://paytoshi.org/faucets?utm_source=faucet&utm_medium=referral&utm_campaign=deploy">Paytoshi faucet list</a></div>')
;

UPDATE paytoshi_settings SET `value` = :password WHERE `name` = 'password';

CREATE TABLE IF NOT EXISTS `paytoshi_recipients` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `address` varchar(65) NOT NULL,
    `earning` int(11) NOT NULL,
    `referral_earning` int(11) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `address` (`address`)
);

ALTER TABLE  `paytoshi_recipients` ADD INDEX (`address`) ;

CREATE TABLE IF NOT EXISTS `paytoshi_payouts` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `recipient_address` varchar(65) NOT NULL,
    `referral_recipient_address` varchar(65) NULL,
    `earning` int(11) NOT NULL,
    `referral_earning` int(11) NOT NULL,
    `ip` varchar(15) NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
);

ALTER TABLE  `paytoshi_payouts` ADD INDEX (`recipient_address`) ;
ALTER TABLE  `paytoshi_payouts` ADD INDEX (`ip`) ;