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
    ('cookie_secret_key', :cookie_secret_key),
    ('rewards', '100*75.25,200*15,300'),
    ('waiting_interval', '3600'),
    ('referral_percentage', '30'),
    ('captcha_provider', 'solve_media'),
    ('solve_media_challenge_key', ''),
    ('solve_media_verification_key', ''),
    ('solve_media_authentication_key', ''),
    ('recaptcha_private_key', ''),
    ('recaptcha_public_key', ''),
    ('theme', 'default'),
    ('version', 1),
    ('default_css', 'body {
            background: #333;
            color: white;
        }

        .title {
            font-size: 48px;
        }

        .headline {
        }

        .rewards {
        }

        .reward-list {
            margin: 16px;
        }

        .reward {
            color:white;
            background-color: #4D90FE;
            border: 1px solid transparent;
            border-radius:15px;
            padding:3px 20px;
            height:30px;
            font-weight:bold;
        }

        .skyscraper-ad {
            margin: 8px;
            text-align: center;
        }

        .leaderboard-ad {
            margin: 8px;
            text-align: center;
        }

        .banner-ad {
            margin: 8px 0 0 0;
            height: 60px;
        }

        .referral-box {
            margin: 36px 0 0 0;
            height: 60px;
            background-color: #5bc0de;
        }

        .faucet {
            width: 468px;
            margin-left: auto;
            margin-right: auto;
        }

        #footer {
          min-height: 80px;
          margin: 50px 0 0 0;
        }'),
    ('default_header_box', 'HEADER'),
    ('default_left_box', 'LEFT'),
    ('default_right_box', 'RIGHT'),
    ('default_footer_box', 'FOOTER'),
    ('default_center1_box', 'CENTER1'),
    ('default_center2_box', 'CENTER2'),
    ('default_center3_box', 'CENTER3')
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