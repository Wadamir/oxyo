<?php
// revision 2026-09-17

// Heading
$_['heading_title']         = 'Массовое начисление баллов';
$_['heading_title_setting'] = 'Настройки - Массовое начисление баллов';

// Text
$_['text_extension']        = 'Модули';
$_['text_success']          = 'Настройки успешно изменены!';
$_['text_success_reward_add'] = 'Бонусные баллы успешно начислены!';
$_['text_list']             = 'Список покупателей';
$_['text_edit']             = 'Редактирование';
$_['text_all']              = 'Все';

// Column
$_['column_name']           = 'Имя покупателя';
$_['column_telephone']      = 'Телефон';
$_['column_email']          = 'E-Mail';
$_['column_customer_group'] = 'Группа покупателей';
$_['column_customer_reward'] = 'Баллы';
$_['column_order_count']     = 'Кол-во заказов';
$_['column_orders_total']    = 'Сумма заказов';
$_['column_action']         = 'Действие';

// Entry
$_['entry_name']            = 'Имя покупателя';
$_['entry_telephone']       = 'Телефон';
$_['entry_email']           = 'Email';
$_['entry_customer_group']  = 'Группа покупателей';
$_['entry_date_registered'] = 'Дата регистрации';
$_['entry_points']          = 'Бонусные баллы';
$_['entry_point_total']     = 'Баланс бонусных баллов';
$_['entry_date_added']      = 'Дата заказа';
$_['entry_min']             = 'от';
$_['entry_max']             = 'до';
$_['entry_order_status']    = 'Статус заказа';
$_['entry_order_count']     = 'Количество заказов';
$_['entry_order_total']     = 'Сумма заказов';
$_['entry_country']         = 'Страна';
$_['entry_region']          = 'Регион';
$_['entry_city']            = 'Город';
$_['entry_store']           = 'Магазин';
$_['entry_current_day']     = 'Учитывать текущий день';
$_['entry_filter_inversion'] = 'Кроме выбранных условий';

$_['entry_limit'] = 'Кол-во писем за раз';
$_['help_limit'] = 'Оставьте пустым если без ограничений';

$_['entry_cron_path'] = 'Команда крон:';

$_['entry_all_customer_filter'] = 'Все покупатели, которые попадают под фильтр';
$_['entry_customer_selected'] = 'Выбранные покупатели для начисления баллов';
$_['entry_reward'] = 'Бонусные баллы';
$_['entry_reward_days'] = 'Срок жизни баллов, дней';
$_['help_reward_days'] = '0 - срок жизни без ограничения';
$_['entry_description'] = 'Описание';
$_['help_description'] = 'Описание начисления в таблице c баллами в ЛК покупателя';
$_['entry_send_mail'] = 'Отправить письма о начислении баллов';


//Tabs
$_['tab_general'] = 'Параметры рассылки';
$_['tab_mail'] = 'Шаблон письма и текст письма рассылки';

//Buttons
$_['button_reward'] = 'Начислить баллы';
$_['button_clear'] = 'Очистить';
$_['button_setting'] = 'Настройки';
$_['button_return'] = 'Вернуться в систему поощрения клиента';
$_['button_show_shortcodes'] = 'Показать список шорт-кодов';
$_['button_hide_shortcodes'] = 'Скрыть список шорт-кодов';
$_['button_emoji'] = 'Вставить emoji';
$_['button_add_shortcode'] = 'Вставить шорт-код';

//Панель параметров рассылки
$_['text_mail_msg']         = 'Тема и текст начисления для рассылки';
$_['entry_mail_description'] = 'Описание для рассылки';

//Mail templates
$_['entry_mail_subject'] = 'Тема письма';
$_['entry_mail_body'] = 'Тело письма';

$_['text_template_new'] = 'Новый шаблон';
$_['text_name_template'] = 'Название шаблона';
$_['text_attention'] = 'Внимание!';
$_['text_unique_name'] = 'Название должно быть уникальным';

$_['text_success_add_template'] = 'Шаблон успешно добавлен!';
$_['text_success_edit_template'] = 'Шаблон успешно сохранен!';
$_['text_success_delete_mail_template'] = 'Шаблон успешно удален!';

// Help
$_['text_shortcodes_legend'] = 'Список шорт-кодов которые можно вставить в тему и тело письма<br><br>
  <b>{STORE_LOGO}</b> - Лого-магазина<br>
  <b>{STORE_NAME}</b> - Названия магазина<br>
  <b>{STORE_URL}</b> - URL адрес магазина<br>
  <b>{FIRSTNAME}</b> - Имя покупателя<br>
  <b>{LASTNAME}</b> - Фамилия покупателя<br>
  <b>{REWARD_RECEIVED}</b> - Сколько начислено баллов<br>
  <b>{REWARD_TOTAL}</b> - Сумма бонусного счета<br>
  <b>{REWARD_DATE_VALID}</b> - Дата по которое действуют начисленные баллы (включительно)<br>
  <b>{REWARD_DATE_OFF}</b> - Дата когда баллы сгорят<br>
  <b>{REWARD_PAGE}</b> - URL страницы ЛК баллов покупателя<br>
  <b>{CURR_DATE}</b> - Текущая дата<br><br>
  {REWARD_RECEIVED}, {REWARD_TOTAL} выводят XX баллов со склонением, поправить формы можно в текстовом файле: admin/language/[КОД-ЯЗЫКА]/mail/mass_customer_reward_add.php';

$_['short_codes'] = array(
  '{STORE_LOGO}' => 'Лого-магазина',
  '{STORE_NAME}' => 'Названия магазина',
  '{STORE_URL}' => 'URL адрес магазина',
  '{FIRSTNAME}' => 'Имя покупателя',
  '{LASTNAME}' => 'Фамилия покупателя',
  '{REWARD_RECEIVED}' => 'Сколько начислено баллов',
  '{REWARD_TOTAL}' => 'Сумма бонусного счета',
  '{REWARD_DATE_VALID}' => 'Дата действия (включительно)',
  '{REWARD_DATE_OFF}' => 'Дата сгорания',
  '{DESCRIPTION}' => 'Описание рассылки',
  '{REWARD_PAGE}' => 'URL ЛК баллов покупателя',
  '{CURR_DATE}' => 'Текущая дата'
);

// Error
$_['error_warning']         = 'Внимательно проверьте форму на ошибки!';
$_['error_permission']      = 'У вас недостаточно прав для начисления баллов!';
$_['error_customers']       = 'Не выбраны покупатели!';
$_['error_reward']          = 'Необходимо указать кол-во начисляемых баллов';
$_['error_description']     = 'Описание (%s) не может быть пустым!';
$_['error_subject']         = 'Тема письма (%s) не может быть пустой!';
$_['error_body']            = 'Тело письма (%s) не может быть пустой!';
$_['error_no_newsletter_template'] = 'Не найдет шаблон для рассылки';
$_['error_customer_empty_email'] = 'Пустой email';
$_['error_template_language'] = 'Для языка `%s` не задан шаблон письма!';
