<?php
// Heading
//revision 2026-07-15
$_['heading_title']         = 'Mass Reward Points Accrual';
$_['heading_title_setting'] = 'Settings - Mass Reward Points Accrual';

// Text
$_['text_extension']        = 'Extensions';
$_['text_success']          = 'Settings updated successfully!';
$_['text_success_reward_add'] = 'Bonus points have been successfully accrued!';
$_['text_list']             = 'Customer List';
$_['text_edit']             = 'Edit';
$_['text_all']              = 'All';

// Column
$_['column_name']           = 'Customer Name';
$_['column_telephone']      = 'Phone';
$_['column_email']          = 'E-Mail';
$_['column_customer_group'] = 'Customer Group';
$_['column_customer_reward'] = 'Points';
$_['column_order_count']     = 'Order Count';
$_['column_orders_total']    = 'Order Total';
$_['column_action']         = 'Action';

// Entry
$_['entry_name']            = 'Customer Name';
$_['entry_telephone']       = 'Phone';
$_['entry_email']           = 'Email';
$_['entry_customer_group']  = 'Customer Group';
$_['entry_date_registered'] = 'Registration Date';
$_['entry_points']          = 'Bonus Points';
$_['entry_point_total']     = 'Bonus Points Balance';
$_['entry_date_added']      = 'Order Date';
$_['entry_min']             = 'from';
$_['entry_max']             = 'to';
$_['entry_order_status']    = 'Order Status';
$_['entry_order_count']     = 'Order Count';
$_['entry_order_total']     = 'Order Total';
$_['entry_country']         = 'Country';
$_['entry_region']          = 'Region';
$_['entry_city']            = 'City';
$_['entry_store']           = 'Store';
$_['entry_current_day']     = 'Include current day';
$_['entry_filter_inversion'] = 'Except selected conditions';

$_['entry_limit'] = 'Number of emails per batch';
$_['help_limit'] = 'Leave empty if unlimited';

$_['entry_cron_path'] = 'Cron command:';

$_['entry_all_customer_filter'] = 'All customers matching the filter';
$_['entry_customer_selected'] = 'Selected customers for points accrual';
$_['entry_reward'] = 'Bonus Points';
$_['entry_reward_days'] = 'Points validity period, days';
$_['help_reward_days'] = '0 - unlimited validity period';
$_['entry_description'] = 'Description';
$_['help_description'] = 'Description of the accrual in the points table in the customer\'s personal account';
$_['entry_send_mail'] = 'Send points accrual emails';

//Tabs
$_['tab_general'] = 'Mail Parameters';
$_['tab_mail'] = 'Email Template and Mail Body';

//Buttons
$_['button_reward'] = 'Accrue Points';
$_['button_clear'] = 'Clear';
$_['button_setting'] = 'Settings';
$_['button_return'] = 'Return to Customer Reward System';
$_['button_show_shortcodes'] = 'Show Shortcodes List';
$_['button_hide_shortcodes'] = 'Hide Shortcodes List';

//Mail templates
$_['entry_mail_subject'] = 'Email Subject';
$_['entry_mail_body'] = 'Email Body';

$_['text_template_new'] = 'New Template';
$_['text_name_template'] = 'Template Name';
$_['text_attention'] = 'Attention!';
$_['text_unique_name'] = 'Name must be unique';

$_['text_success_add_template'] = 'Template added successfully!';
$_['text_success_edit_template'] = 'Template saved successfully!';
$_['text_success_delete_mail_template'] = 'Template deleted successfully!';

// Help
$_['text_shortcodes_legend'] = 'List of shortcodes that can be inserted into the subject and body of the email<br><br>
  <b>{STORE_LOGO}</b> - Store Logo<br>
  <b>{STORE_NAME}</b> - Store Name<br>
  <b>{STORE_URL}</b> - Store URL<br>
  <b>{FIRSTNAME}</b> - Customer First Name<br>
  <b>{LASTNAME}</b> - Customer Last Name<br>
  <b>{REWARD_RECEIVED}</b> - Amount of points accrued<br>
  <b>{REWARD_TOTAL}</b> - Bonus account balance<br>
  <b>{REWARD_DATE_VALID}</b> - Date through which the accrued points are valid (inclusive)<br>
  <b>{REWARD_DATE_OFF}</b> - Date when points expire<br>
  <b>{REWARD_PAGE}</b> - URL of the customer\'s points page in personal account<br>
  <b>{CURR_DATE}</b> - Current date<br><br>
  {REWARD_RECEIVED}, {REWARD_TOTAL} display "XX points" with declension, you can adjust the forms in the text file: admin/language/[LANGUAGE-CODE]/mail/mass_customer_reward_add.php';

// Error
$_['error_warning']         = 'Please check the form carefully for errors!';
$_['error_permission']      = 'You do not have sufficient permissions to accrue points!';
$_['error_customers']       = 'No customers selected!';
$_['error_reward']          = 'Please specify the number of points to accrue';
$_['error_description']     = 'Description (%s) cannot be empty!';
$_['error_subject']         = 'Email Subject (%s) cannot be empty!';
$_['error_body']            = 'Email Body (%s) cannot be empty!';
$_['error_no_newsletter_template'] = 'Newsletter template not found';
$_['error_customer_empty_email'] = 'Empty email';