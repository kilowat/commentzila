# Модуль комментариев для cms Bitrix "Commentzila"
Модуль позволяет добавить к вашим страницам древовидные комментарии.
Описание:
Внимание модуль на данном этапе работает только с Mysql базой данных, для других баз будет выпущена версия позже.
Модуль содержит компонент, после установки он будет находиться в разделе компонентов Aliens.pro, просто перетащите его на страницу в публичной части и настройте.
Также в админ панеле  раздела сервис появится пункт меню по управлению комментариями.
Комментарии содержат рейтинг понравился/не понравился, который доступен только для зарегистрированных пользователей.

Особенности:
- Определения страны клиента оставившего комментарий c помощью tabgeo;
- Обязательная / не обязательная регистрация;
- Настройка глубины вложенности дерева комментариев;
- Возможность удалять и помечать комментарии как спам через админ панель;
- Возможность под администратором удалять комментарии через публичку;
- Настраиваемый таймаут на добавление комментариев;
- Настройка максимального кол-во символов в комментариях;
- Настройка вывода комментариев по дате добавления, по возрастанию и убыванию;
- В качестве постраничной навигации комментариев, используется Paginator 2000;
- Не требуется Jquery.

Привязка комментариев к странице (по умолчанию комментарии привязываются к uri на котором расположен компонент):

1) Если вы размещаете компонент комментариев на статической странице, настраивать привязку к элементам не нужно, модуль в этом случае по умолчанию привязывается к текущему uri. Внимание в этом случае если вы смените uri для данной странице то комментарии оставленные ранее не будут привязаны к новому uri.

2) Если вы хотите привязать комментарии к динамической странице - например страница с детальным элементом. То можно указать в настройках тип инфоблока и параметр в который передается id элемента или код элемента чтобы привязаться, в этом случае комментарии будут привязаны к элементу и смена uri не повлияет на привязку.

Кеширование:
Компонент поддерживает кеширование, по умолчанию оно включено. Если на вашем сайте планируется не частые добавления комментариев, то с кешированием будет меньше нагрузки на бд, но если у вас очень много частых добавлений, то возможно будет лучше если кеш отключить.

Поля ФИО и аватар:
Если вы установили параметр который требует авторизацию для оставления комментариев, то ФИО и аватар берутся из полей профиля пользователя. Если данные поля не заполнены то в качестве имени будет префикс и его id - пользователь_id, а в качестве аватара изображение по умолчанию.
