<?
/**
 * Copyright (c) 27/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$MESS['fly.popup_MAIN_TITLE'] = 'Всплывающие окна FLY';

$MESS['fly.popup_LIST_TITLE'] = 'Всплывающие окна FLY';
$MESS['fly.popup_LIST_CREATE_NEW_POPUP'] = 'Создать всплывающее окно';

$MESS['fly.popup_TAB_CONDITION_NAME'] = 'Условия показа';
$MESS['fly.popup_TAB_CONDITION_DESC'] = 'Условия показа всплывающего окна';
$MESS['fly.popup_TAB_SETTING_NAME'] = 'Оформление';
$MESS['fly.popup_TAB_SETTING_DESC'] = 'Оформление всплывающего окна';
$MESS['fly.popup_TAB_CONDITION_CONTACT'] = 'Сборщик контактов';
$MESS['fly.popup_TAB_CONTACT_DESC'] = 'Настройка сборщика контактов';
$MESS['fly.popup_TAB_BUTTON_SAVE'] = 'Сохранить';
$MESS['fly.popup_TAB_BUTTON_APPLY'] = 'Применить';
$MESS['fly.popup_TAB_BUTTON_ADD'] = 'Добавить окно';
$MESS['fly.popup_TAB_BUTTON_CANCEL'] = 'Отмена';
$MESS['fly.popup_COND_service_section'] = 'Служебные настройки';
$MESS['fly.popup_COND_main_section'] = 'Основные условия';

$MESS['fly.popup_VIEWS'] = 'Выберите шаблон';
$MESS['fly.popup_VIEW_video'] = 'Видео';
$MESS['fly.popup_VIEW_image'] = 'Баннер (картинка слева)';
$MESS['fly.popup_VIEWS_STEP_1'] = 'Шаг 1. Выберите тип всплывающего окна';
$MESS['fly.popup_VIEWS_STEP_2'] = 'Шаг 2. Выберите шаблон и цветовую схему';
$MESS['fly.popup_VIEWS_STEP_DEMO'] = 'Демонстрация окна';
$MESS['fly.popup_VIEWS_STEP_3'] = 'Шаг 3. Заполните необходимые поля и настройки';

$MESS['fly.popup_IMG_BLOCK_TITLE'] = 'Заменить картинку';
$MESS['fly.popup_IMG_BLOCK_ALTIMG'] = 'Кликните на картинку для замены';
$MESS['fly.popup_IMG_SHOWALL'] = 'Показать все';
$MESS['fly.popup_IMG_HIDEALL'] = 'Скрыть';
$MESS['fly.popup_IMG_BLOCK_DELIMG'] = 'Удалить картинку';
$MESS['fly.popup_IMG_BLOCK_UPLOADIMG'] = 'Загрузите картинку';

$MESS['fly.popup_TABCOND_TITLE_MAIN']='Общие условия';
$MESS['fly.popup_TABCOND_TITLE_EVENTS']='Показ по событиям';
$MESS['fly.popup_TABCOND_TITLE_ADDITIONAL']='Дополнительные условия';
$MESS['fly.popup_TABCOND_TITLE_SALE']='Для интернет-магазина';
$MESS['fly.popup_TABCOND_ACTIVE']='Активность';
$MESS['fly.popup_TABCOND_ACTIVE_HINT']='Служит для <b>включения/выключения</b> работы всплывающего окна';
$MESS['fly.popup_TABCOND_SERVICE_NAME']='Служебное имя';
$MESS['fly.popup_TABCOND_SERVICE_NAME_HINT']='Служит <b>для удобства работы</b> с модулем.<hr>Данное имя отображается только в админке';
$MESS['fly.popup_TABCOND_SORT']='Приоритет';
$MESS['fly.popup_TABCOND_SORT_HINT']='Служит для определения <b>какое окно показывать</b> при совпадении остальных условий показа.<hr>По умолчанию равен 500, чем ниже значение, тем выше приоритет срабатывания';
$MESS['fly.popup_TABCOND_SITE']='Показывать на сайтах';
$MESS['fly.popup_TABCOND_SITE_HINT']='Позволяет выбрать <b>на каком сайте</b> будет показываться всплывающее окно';
$MESS['fly.popup_TABCOND_GROUPS']='Группа пользователей';
$MESS['fly.popup_TABCOND_GROUPS_HINT']='Позволяет указать <b>каким группам пользователей</b> показывать всплывающее окно';
$MESS['fly.popup_TABCOND_ACTIVE_FROM']='Дата начала показа';
$MESS['fly.popup_TABCOND_ACTIVE_FROM_HINT']='Позволяет указать <b>дату с которой</b> начнет показываться всплывающее окно';
$MESS['fly.popup_TABCOND_ACTIVE_TO']='Дата окончания показа';
$MESS['fly.popup_TABCOND_ACTIVE_TO_HINT']='Позволяет указать <b>дату до которой</b> будет показываться всплывающее окно';
$MESS['fly.popup_TABCOND_WINDOW_POSITION']='Позиция окна';
$MESS['fly.popup_TABCOND_WINDOW_POSITION_VERTICAL']='Вертикальная';
$MESS['fly.popup_TABCOND_WINDOW_POSITION_VERTICAL_UP']='Сверху';
$MESS['fly.popup_TABCOND_WINDOW_POSITION_VERTICAL_DOWN']='Снизу';
$MESS['fly.popup_TABCOND_WINDOW_POSITION_HORIZONTAL']='Горизонтальная';
$MESS['fly.popup_TABCOND_WINDOW_POSITION_HORIZONTAL_LEFT']='Слева';
$MESS['fly.popup_TABCOND_WINDOW_POSITION_HORIZONTAL_RIGHT']='Справа';
$MESS['fly.popup_TABCOND_SHOWONLYPATH']='Маска включения';
$MESS['fly.popup_TABCOND_SHOWONLYPATH_HINT']='Служит для указания адресов разделов или страниц, на которых <b>будет показываться</b> всплывающее окно.<hr>Адрес страницы необходимо указывать без протокола (http) и без домена (мойсайт.рф), т.е. просто /contacts/ - адрес страницы контактов.<br>Также в обработке адреса используется знак *, который подразумевает любой символ в адресе.<br>Например /catalog/* - будет отображать всплывающее окно на всех страницах каталога.<br>Например, *?utm_source=vk* - для всех переходов по UTM меткам с ВК.<hr>Более подробная информация по настройке данного параметра доступна в <a href="https://fly.ru/documentation/popup/lesson61/" target="_blank">документации</a>.';
$MESS['fly.popup_TABCOND_SHOWONLYPATH_EXAMPLE']='Например, /catalog/';
$MESS['fly.popup_TABCOND_HIDEONLYPATH']='Маска исключения';
//$MESS['fly.popup_TABCOND_HIDEONLYPATH_HINT']='Служит для указания адресов разделов или страниц, на которых <b>НЕ будет показываться</b> всплывающее окно.<br><br>Адрес страницы необходимо указывать без протокола (http) и без домена (мойсайт.рф), т.е. просто /contacts/ - адрес страницы контактов.<hr>Более подробную информацию по настройке данного параметра вы можете найти в <a href="https://fly.ru/documentation/popup/" target="_blank">документации модуля</a>';
$MESS['fly.popup_TABCOND_HIDEONLYPATH_HINT']='Служит для указания адресов разделов или страниц, на которых <b>НЕ будет показываться</b> всплывающее окно.<hr>Адрес страницы необходимо указывать без протокола (http) и без домена (мойсайт.рф), т.е. просто /contacts/ - адрес страницы контактов.<br>Также в обработке адреса используется знак *, который подразумевает любой символ в адресе.<br>Например /catalog/* - будет отображать всплывающее окно на всех страницах каталога.<br>Например, *?utm_source=vk* - для всех переходов по UTM меткам с ВК.<hr>Более подробная информация по настройке данного параметра доступна в <a href="https://fly.ru/documentation/popup/lesson62/" target="_blank">документации</a>.';
$MESS['fly.popup_TABCOND_HIDEONLYPATH_EXAMPLE']='Например, /order/make/';
$MESS['fly.popup_TABCOND_PRIORITY_NOTSHOW']='Сначала маска исключения';
$MESS['fly.popup_TABCOND_PRIORITY_SHOW']='Сначала маска включения';
$MESS['fly.popup_TABCOND_AFTERSHOWCOUNTPAGES']='После просмотра N страниц';
$MESS['fly.popup_TABCOND_AFTERSHOWCOUNTPAGES_HINT']='Позволяет настроить отображение всплывающего окна только <b>после просмотра указанного числа страниц</b> сайта';
$MESS['fly.popup_TABCOND_AFTERTIMESECOND']='После S секунд на сайте';
$MESS['fly.popup_TABCOND_AFTERTIMESECOND_HINT']='Позволяет настроить отображение всплывающего окна <b>после пребывания</b> пользователя на страницах сайта <b>свыше указанного числа секунд</b>';
$MESS['fly.popup_TABCOND_TIMEINTERVAL']='Интервал показа';
$MESS['fly.popup_TABCOND_TIMEINTERVAL_HINT']='Служит для <b>указания времени работы</b> всплывающего окна.<br><br>Например, с 20:00 до 22:00';
$MESS['fly.popup_TABCOND_TIMEINTERVAL_FROM']='От';
$MESS['fly.popup_TABCOND_TIMEINTERVAL_TO']='До';
$MESS['fly.popup_TABCOND_ANCHORVISIBLE']='Имя якоря (a name=)';
//$MESS['fly.popup_TABCOND_ANCHORVISIBLE_HINT']='Поместите якорь на любую страницу сайта. Как только он <b>попадет в зону видимости</b> браузера, появится всплывающее окно.<br><br>Например, вы можете поместить якорь в конце какой-то статьи и как только пользователь дочитает статью до данного якоря, появится всплывающее окно.<hr>Более подробную информацию по настройке данного параметра вы можете найти в <a href="https://fly.ru/documentation/popup/" target="_blank">документации модуля</a>';
$MESS['fly.popup_TABCOND_ANCHORVISIBLE_HINT']='Поместите якорь на любую страницу сайта. Как только он <b>попадет в зону видимости</b> браузера, появится всплывающее окно.<br><br>Например, вы можете поместить якорь в конце какой-то статьи и как только пользователь дочитает статью до данного якоря, появится всплывающее окно.<hr>Более подробная информация по настройке данного параметра будет представлена в документации модуля в ближайшее время.';
$MESS['fly.popup_TABCOND_ONCLICKCLASSLINK']='Класс ссылки (a class=)';
//$MESS['fly.popup_TABCOND_ONCLICKCLASSLINK_HINT']='Добавьте ссылку с уникальным классом в любое место на вашем сайте. При нажатии на нее появится всплывающее окно.<br><br>Например, разместите ссылку на детальной странице товара, при нажатии на нее появится всплывающее окно.<hr>Более подробную информацию по настройке данного параметра вы можете найти в <a href="https://fly.ru/documentation/popup/" target="_blank">документации модуля</a>';
$MESS['fly.popup_TABCOND_ONCLICKCLASSLINK_HINT']='Добавьте ссылку с уникальным классом в любое место на вашем сайте. При нажатии на нее появится всплывающее окно.<br><br>Например, разместите ссылку на детальной странице товара, при нажатии на нее появится всплывающее окно.<hr>Более подробная информация по настройке данного параметра будет представлена в документации модуля в ближайшее время.';
$MESS['fly.popup_TABCOND_ALREADYGOING']='При попытке уйти с сайта';
$MESS['fly.popup_TABCOND_ALREADYGOING_HINT']='Всплывающее окно будет показываться только <b>при попытке пользователя уйти с сайта</b> (перемещение курсора мыши вверх окна браузера).<br><br>Данная функция <b>работает только на компьютерах!</b><hr>Более подробная информация по настройке данного параметра доступна в <a href="https://fly.ru/documentation/popup/lesson68/" target="_blank">документации</a>.';
$MESS['fly.popup_TABCOND_REPEATTIME']='Повторить показ пользователю через';
$MESS['fly.popup_TABCOND_REPEATTIME_HINT']='Служит для указания <b>через который промежуток</b> всплывающее окно <b>вновь будет показано</b> одному пользователю.<hr>Более подробная информация по настройке данного параметра доступна в <a href="https://fly.ru/documentation/popup/lesson67/" target="_blank">документации</a>.';
$MESS['fly.popup_TABCOND_REPEATTYPE_HOUR']='Час';
$MESS['fly.popup_TABCOND_REPEATTYPE_DAY']='День';
$MESS['fly.popup_TABCOND_REPEATTYPE_WEEK']='Неделя';
$MESS['fly.popup_TABCOND_REPEATTYPE_MONTH']='Месяц';
$MESS['fly.popup_TABCOND_REPEATTYPE_YEAR']='Год';
$MESS['fly.popup_TABCOND_SALECOUNTPRODUCT']='Если в корзине N товаров';
$MESS['fly.popup_TABCOND_SALECOUNTPRODUCT_HINT']='Всплывающее окно будет отображаться пользователю только если у него <b>в корзине столько товаров</b> (или более) сколько вы указали';
$MESS['fly.popup_TABCOND_SALEADDPRODUCT']='Добавить';
$MESS['fly.popup_TABCOND_SALESUMMBASKET']='Если сумма корзины выше N рублей';
$MESS['fly.popup_TABCOND_SALESUMMBASKET_HINT']='Всплывающее окно будет отображаться пользователю только если <b>сумма товаров в корзине равна</b> (или выше) суммы, которую вы указали';
$MESS['fly.popup_TABCOND_SALEIDPRODINBASKET']='Если в корзине есть указанный товар';
$MESS['fly.popup_TABCOND_SALEIDPRODINBASKET_HINT']='Всплывающее окно будет показано пользователю только если <b>в его корзине есть указанный товар</b> или товары';

$MESS['fly.popup_SUCCESS_UPDATE']='Окно ID успешно изменёно.';
$MESS['fly.popup_SUCCESS_ADD']='Окно ID успешно добавлено.';
$MESS['fly.popup_ERROR_SAVE']='Ошибка сохранения';

$MESS['fly.popup_TABLELIST_PAGINATOR']='Страница';
$MESS['fly.popup_TABLELIST_NAME']='Служебное имя';
$MESS['fly.popup_TABLELIST_SORT']='Приоритет';
$MESS['fly.popup_TABLELIST_TYPE']='Тип контента';
$MESS['fly.popup_TABLELIST_STAT_SHOW']='Окно показано';
$MESS['fly.popup_TABLELIST_STAT_TIME']='Общее время показа';
$MESS['fly.popup_TABLELIST_STAT_ACTION']='Целевое действие';
$MESS['fly.popup_TABLELIST_ACTIVE']='Активность';
$MESS['fly.popup_TABLELIST_SITES']='Сайты';
$MESS['fly.popup_TABLELIST_MASK_IN']='Маска включения';
$MESS['fly.popup_TABLELIST_MASK_OUT']='Маска исключения';

$MESS['fly.popup_NO']='Нет';
$MESS['fly.popup_YES']='Да';
$MESS['fly.popup_LOGIC_INFO']='ВНИМАНИЕ! У всех параметров работает логика - "И". Т.е. всплывающее окно будет показано только если выполнены ВСЕ отмеченные условия!<br>Исключение составляют параметры блока "Показ по событиям"';
$MESS['fly.popup_JSADM_FILES']='Добавить файлы';

$MESS['fly.popup_TABLE_EDIT']='Редактировать';
$MESS['fly.popup_TABLE_ADDITIONAL']='Дополнительно';
$MESS['fly.popup_TABLE_DELETE']='Удалить';
$MESS['fly.popup_TABLE_DELETE_CONFIRM']='Действительно удалить?';
$MESS['fly.popup_TABLE_COPY']='Копировать';
$MESS['fly.popup_TABLE_COPY_CONFIRM']='Сделать копию?';
$MESS['fly.popup_SAVE_ERROR']='Ошибка обновления';
$MESS['fly.popup_POPUP_EMPTY']='Окно не найдено';
$MESS['fly.popup_ERROR_CONTACT_TAB_SETTING']='Настройки данного окна нужны только для окна типа "Сборщик контактов"';
$MESS['fly.popup_HIDE_BLOCK']='Скрыть';
$MESS['fly.popup_SHOW_BLOCK']='Показать';
$MESS['fly.popup_CREATE_BLOCK']='Создать';
$MESS['fly.popup_APPLY']='Применить';
$MESS['fly.popup_NAMEISREQUIRED_BLOCK']='Название обязательно!';
$MESS['fly.popup_COLORTHEME_CREATESUCCESS']='Цветовая схема успешно создана';
$MESS['fly.popup_CUSTOMTEMPLATE_CREATESUCCESS']='Шаблон успешно создан';


$MESS['fly.popup_CONTACT_NAME']='Сохранение контактов';
$MESS['fly.popup_CONTACT_SEND_MAIL']='Отправлять письмо менеджеру';
$MESS['fly.popup_CONTACT_SEND_MAIL_HINT']='При успешном заполнении формы менеджеру будет отсылаться письмо с данными пользователя';
$MESS['fly.popup_CONTACT_EMAIL_TEMPLATE']='шаблон';
$MESS['fly.popup_CONTACT_SAVE_TO_LIST']='Добавлять Email в список адресов';
$MESS['fly.popup_CONTACT_SAVE_TO_LIST_HINT']='Email пользователя будет добавлен в список адресов модуля Email-маркетинг<br><br><a href="/bitrix/admin/sender_contact_admin.php?lang=ru" target="_blank">Перейти</a> к списку адресов';
$MESS['fly.popup_CONTACT_SAVE_TO_IBLOCK']='Сохранять заявку в инфоблок';
$MESS['fly.popup_CONTACT_SAVE_TO_IBLOCK_HINT']='При успешном заполнении формы информация о пользователе будет сохраняться в элемент инфоблока';
$MESS['fly.popup_CONTACT_SAVE_LIST_IBLOCK']='Инфоблок';
$MESS['fly.popup_CONTACT_REGISTER']="Регистрировать пользователя при отправке контактов";
$MESS['fly.popup_CONTACT_REGISTER_HINT']="Регистрация будет происходить только в случае сбора Email пользователя и использует стандартный шаблон автоматической регистрации по Email";
$MESS['fly.popup_EVENT_TYPE_NAME']='Заполнена форма fly_popup';
$MESS['fly.popup_POST_SEND']='Заполнена форма fly_popup';
$MESS['fly.popup_CONSENT_OUT']='Для активации функционала соглашений необходимо настроить как минимум одно <a href="/bitrix/admin/agreement_admin.php?lang='.LANG.'" target="_blank">соглашение</a>';

$MESS['fly.popup_TIMER_NAME']='Таймер';
$MESS['fly.popup_TIMER_ENABLE']='Отображать таймер';
$MESS['fly.popup_TIMER_ENABLE_HINT']='Таймер обратного отсчета для окна';
$MESS['fly.popup_TIMER_TIME']='Время до';
$MESS['fly.popup_TIMER_TIME_HINT']='popup_TIMER_NAME';
$MESS['fly.popup_TIMER_TIME_TITLE']='Нажмите для выбора даты и времени';
$MESS['fly.popup_TIMER_TEXT']='Текст таймера';
$MESS['fly.popup_TIMER_TEXT_DEFAULT']='Это предложение закончится через';
$MESS['fly.popup_TIMER_TEXT_HINT']='';
$MESS['fly.popup_TIMER_LEFT']='Слева';
$MESS['fly.popup_TIMER_RIGHT']='Справа';
$MESS['fly.popup_TIMER_TOP']='Сверху';
$MESS['fly.popup_TIMER_BOTTOM']='Снизу';


$MESS['fly.popup_ROULETTE_SORT']='Сортировка';
$MESS['fly.popup_ROULETTE_TEXT']='Текст';
$MESS['fly.popup_ROULETTE_COLOR']='Цвет';
$MESS['fly.popup_ROULETTE_RULE']='Результат';
$MESS['fly.popup_ROULETTE_RULE_INFO']='Подробнее';
$MESS['fly.popup_ROULETTE_RULE_INFO_BASIC']='В случае выпадение варианта «Выигрыш», пользователю будет отправлено письмо на основе выбранного выше шаблона.<br>В случае выпадения варианта «Ничего», пользователь увидит только оповещение о неудаче';
$MESS['fly.popup_ROULETTE_RULE_INFO_SALE']='<br><hr>В случае выпадения варианта из группы «Правила работы с корзиной», пользователю будет отправлено письмо на основе выбранного выше шаблона с купоном выбранного правила работы с корзиной со сроком действия который был введен выше';
$MESS['fly.popup_ROULETTE_RULES']='Правила работы с корзиной';
$MESS['fly.popup_ROULETTE_CONTROL']='Управление';
$MESS['fly.popup_ROULETTE_NOTHING']='Ничего';
$MESS['fly.popup_ROULETTE_WIN']='Выигрыш';
$MESS['fly.popup_ROULETTE_MINIMUM']='Минимум 4 элемента';
$MESS['fly.popup_ROULETTE_ADD']='Добавить';
$MESS['fly.popup_ROULETTE_BASIC']='Общее';


$MESS['fly.popup_DISCOUNT_IMG_1']='Логотип';
$MESS['fly.popup_DISCOUNT_IMG_2']='Картинка';



$MESS['fly.popup_JS_SELECT_IMG'] = 'Выберите изображение';
$MESS['fly.popup_ADD_COLOR_THEME'] = 'Добавить цветовую схему на основе ';
$MESS['fly.popup_ADD_TEMPLATE'] = 'Добавить шаблон на основе ';
$MESS['fly.popup_CONFIRM_ADD_COLOR_THEME'] = 'Вы действительно хотите создать дополнительную цветовую схему ';
$MESS['fly.popup_CONFIRM_ADD_TEMPLATE_ENTERNAME'] = 'Ввведите название шаблона';
$MESS['fly.popup_CONFIRM_ADD_COLOR_ENTERNAME'] = 'Ввведите цветовой схемы';
?>