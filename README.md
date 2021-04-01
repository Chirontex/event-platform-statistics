# Event Platform Statistics 1.9.2

Плагин, реализующий сбор статистики на площадке мероприятий.

## Требования

1. WordPress 5.4.2 или выше

2. PHP 7.3 или выше

## Установка

1. Создать новую директорию в wp-content/plugins и скопировать в неё содержимое репозитория.

2. Перейти в консоли в эту директорию и выполнить команду `composer install`.

3. Включать плагин в консоли администратора WordPress.

## Статистика

Выгрузку статистики можно произвести, перейдя в пункт бокового меню "Статистика" в админпанели. Комплектация выгрузки осуществляется чекбоксами, выгрузка производится при нажатии на кнопку "Скачать". Выгрузка статистики скачивается в виде файла с расширением .xlsx и состоит из одного или нескольких листов, в зависимости от выбранной комплектации.

Доступны следующие листы:

1. Участники — персональная информация по участникам: ID в системе, фамилия, имя, отчество, e-mail, телефон, место работы, дата рождения, город, согласие на обработку данных и общее количество подтверждений присутствия.

2. Демография — список городов (или стран и городов) участников и общее количество участников в каждом из них.

3. Посещения — содержит перечисление всех зафиксированных посещений страниц сайта авторизованными пользователями: адрес страницы, дату и время посещения, а также все данные посетителя.

4. Программа (выгружается, если был отмечен чекбокс "НМО") — общий список пунктов программы (задаётся в "Титрах") и информация по ним.

5. НМО — список участников с подсчётом релевантных нажатий на кнопки подтверждения присутствия. Вместо заголовков лекции для краткости используются их ID, которые можно узнать из листа "Программа".

6. НМО (детализация) — общий список нажатий на кнопку подтверждения присутствия во всех залах по всем пользователям с указанием времени нажатия по Мск.

ВАЖНО: на листе "Участники" считается общее количество нажатий на кнопку подтверждения присутствия, а на листе "НМО" — количество релевантных! Соответственно, они могут не совпадать. Т.е. если нажатие на кнопку подтверждения присутствия произошло вне обязательной для НМО программы, то оно будет посчитано на листе "Участники", а на листе "НМО" — нет. Хотя, на листе "НМО (детализация)" оно тоже будет отображено, для полноты картины.

Из всех данных пользователей по умолчанию в выгрузку включаются только ID и e-mail. Если нужно включить метаданные, то сперва их нужно сопоставить с колонками с помощью формы "Сопоставление метаданных пользователей" на странице "Статистика". **ВАЖНО:** на листе "Демография" будут отображены какие-либо данные лишь в том случае, если будет создано сопоставление с именем "Город", либо сопоставления "Страна", "Регион" и "Город".

## Эффект присутствия

Эффект присутствия реализован внутри плагина "Event Platform Statistics".

Шорткод для вставки кнопки подтверждения присутствия называется "eps-presence-effect-button" и может иметь следующие атрибуты:

1. list — служит для связи с титрами, в нём обязательно надо прописать обозначение зала, которое мы задали/зададим при создании титров (см. "Титры"), в противном случае нажатия на эту кнопку не отобразятся в статистике. Значение по умолчанию: "Общий". Если ведётся только одна трансляция, то можно не указывать.

2. id — идентификатор HTML-элемента кнопки. Имеет смысл указывать, если на одной странице ставится несколько кнопок, в противном случае они не будут работать корректно. Если же кнопка подтверждения присутствия на странице только одна, то можно не указывать. Значение по умолчанию: "eps-presence-effect-button".

3. button-class — названия классов CSS, которые будут применены к кнопке. Значения по умолчанию нет.

4. button-style — CSS-стили, которые будут применены к кнопке. Значения по умолчанию нет.

5. message-position — положение сообщения об отправке подтверждения. Может иметь одно из двух возможных значений: "before" (сообщение будет показываться над кнопкой) или "after" (сообщение будет показываться под кнопкой). Значение по умолчанию: "after".

6. message-class — классы CSS, которые будут применены к сообщению об отправке подтверждения. Значения по умолчанию нет.

7. message-style — CSS-стили, которые будут применены к сообщению об отправке подтверждения. Значения по умолчанию нет.

В контенте шорткода прописывается текст, который должен быть на кнопке. Если контент пустой, то пропишется "Подтвердите Ваше присутствие".

Сообщение об отправке подтверждения — это HTML-элемент `<p>` с текстом. Исчезает через 3 секунды после появления.

ПРИМЕР ШОРТКОДА:

`[eps-presence-effect-button list="Комната 1" id="eps-button-room-1" button-style="color: #016c52;" ]Подтвердить присутствие[/eps-presence-effect-button]`

## Титры

Титры реализованы внутри плагина "Event Platform Statistics", поскольку участвуют в формировании статистики.

Работа с титрами ведётся на странице "Титры", на которую можно попасть, кликнув по соответствующему пункту в боковом меню админпанели, там можно добавлять, редактировать и удалять ранее добавленные элементы программы трансляции.

ВАЖНО: Несмотря на то, что интерфейс работы с титрами позволяет одновременно редактировать несколько записей, сохранить изменения единовременно возможно только в одной — таким образом, если вы открыли редактирование одной записи, отредактировали её без сохранения, затем открыли редактирование второй записи, отредактировали её и сохранили, то изменения первой записи не сохранятся.

При добавлении лекции нужно указать:

1. Заголовок — это то, что будет отображаться участникам.

2. Обозначение зала — условное обозначение. Оно может быть любым, но обязательно должно быть одинаковым у лекций, транслирующихся в одном зале. Это обозначение указывается в атрибуте "list" шорткодов для вставки титров и кнопки подтверждения присутствия. Если обозначения не совпадут, то титры и подтверждение присутствия будут работать некорректно.

3. Дата/время начала и дата/время конца — всегда по Мск.

4. Учёт лекции в статистике по НМО — если не отмечено, то лекция не попадёт в лист "НМО" выгрузки статистики. Кнопка проверки эффекта присутствия не будет отображаться во время этой лекции.

Любой из этих параметров можно изменить в любое время. Если заголовок был изменён, то у зрителей во время трансляции он изменится в течение 5 секунд.

Для вставки титров на страницу используется шорткод "eps-title", который может иметь следующие атрибуты:

1. list — указывает на зал, титры которого нужно отобразить, а также служит для связи с кнопками подтверждения пристутствия. Значение этого атрибута обязательно должно совпадать с обозначением зала, которое мы указали при создании титров — в противном случае они не будут отображаться. То же самое значение нужно указывать в аналогичном атрибуте шорткода кнопки подтверждения присутствия, чтобы нажатия на на кнопку воспринимались системой как подтверждение присутствия на лекции, идущей в данный момент в этом зале. Значение по умолчанию: "Общий". Если зал трансляции только один, то можно не указывать.

2. tag — тэг, на основе которого будет создан HTML-элемент титров. Значение по умолчанию: "h3".

3. id — идентификатор, который будет присвоен HTML-элементу титров. Нужно указывать в том случае, если мы ставим на страницу несколько элементов с титрами. Например, у нас несколько залов, и мы хотим, чтобы на странице каждого из них отображались титры всех залов: в этом случае нам нужно прописать количество шорткодов, идентичное количеству залов, и каждому из них присвоить уникальный идентификатор — в противном случае титры не будут работать корректно. Значение по умолчанию: "eps-title".

4. class — классы CSS, которые должны быть применены к HTML-элементу титров. Значения по умолчанию нет.

5. style — CSS-стили, которые должны быть применены к HTML-элементу титров. Значения по умолчанию нет.

В контенте шорткода указывается фраза, которая должна отображаться в то время, на которое у нас не назначено лекций. Если контент шорткода пуст, то будет отображаться фраза "В данный момент ничего не происходит". Если нужно, чтобы ничего не отображалось в это время, можно просто поставить пробел.

ПРИМЕР ШОРТКОДА:

`[eps-title list="Комната 1" tag="h4" id="eps-title-room-1" style="text-align: center;"]Ждём Вас здесь во время трансляции![/eps-title]`

