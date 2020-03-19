<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The file is responsible for handing the mailbox installation
 */

add_option('mailbox_enabled', 1);
add_option('mailbox_imap_server', '');
add_option('mailbox_encryption', '');
add_option('mailbox_folder_scan', 'Inbox');
add_option('mailbox_check_every', 3);
add_option('mailbox_only_loop_on_unseen_emails', 1);

if (!$CI->db->table_exists(db_prefix() . 'mail_inbox')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "mail_inbox` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `from_staff_id` int(11) NOT NULL DEFAULT '0',
      `to_staff_id` int(11) NOT NULL DEFAULT '0',
      `to` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `cc` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `bcc` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `sender_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `subject` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `body` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `has_attachment` tinyint(1) NOT NULL DEFAULT '0',
      `date_received` datetime NOT NULL,
      `read` tinyint(1) NOT NULL DEFAULT '0',
      `folder` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inbox',
      `stared` tinyint(1) NOT NULL DEFAULT '0',
      `important` tinyint(1) NOT NULL DEFAULT '0',
      `trash` tinyint(1) NOT NULL DEFAULT '0',
      `from_email` varchar(150) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mail_outbox')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "mail_outbox` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sender_staff_id` int(11) NOT NULL DEFAULT '0',
      `to` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `cc` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `bcc` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `sender_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `subject` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `body` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `has_attachment` tinyint(1) NOT NULL DEFAULT '0',
      `date_sent` datetime NOT NULL,
      `stared` tinyint(1) NOT NULL DEFAULT '0',
      `important` tinyint(1) NOT NULL DEFAULT '0',
      `trash` tinyint(1) NOT NULL DEFAULT '0',
      `reply_from_id` int(11) DEFAULT NULL,
      `reply_type` varchar(45) NOT NULL DEFAULT 'inbox',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'mail_attachment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "mail_attachment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `mail_id` int(11) NOT NULL,
      `file_name` varchar(191) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `file_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `type` varchar(45) NOT NULL DEFAULT 'inbox',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('draft', 'mail_outbox')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'mail_outbox` ADD COLUMN `draft` tinyint(1) NOT NULL DEFAULT 0;');            
}

if (!$CI->db->field_exists('mail_password', 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`  ADD COLUMN `mail_password` VARCHAR(250) NULL');
}

if (!$CI->db->field_exists('last_email_check', 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`  ADD COLUMN `last_email_check` VARCHAR(50) NULL');
}