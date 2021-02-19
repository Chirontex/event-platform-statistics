# Event Platform Statistics

[![Версия](https://img.shields.io/badge/%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%8F-1.5.7-blue "Версия")](http://https://img.shields.io/badge/%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%8F-1.5.7-blue "Версия")

## Статистика

Плагин статистики обозначен как "Event Platform Statistics", располагается в директории /wp-content/plugins/event-platform-statistics и включает в себя эффект присутствия и титры (работа с эффектом присутствия и титрами описана в разделах "Эффект присутствия" и "Титры").

Выгрузку статистики можно произвести, перейдя в пункт бокового меню "Статистика" в админпанели. Комплектация выгрузки осуществляется чекбоксами, выгрузка производится при нажатии на кнопку "Скачать". Выгрузка статистики скачивается в виде файла с расширением .xlsx и состоит из одного или нескольких листов, в зависимости от выбранной комплектации.

Доступны следующие листы:

1. Участники — персональная информация по участникам: ID в системе, фамилия, имя, отчество, e-mail, телефон, место работы, дата рождения, город, согласие на обработку данных и общее количество подтверждений присутствия.

2. Демография — список городов участников и общее количество участников в каждом из них.

3. Посещения — содержит перечисление всех зафиксированных посещений страниц сайта авторизованными пользователями: адрес страницы, дату и время посещения, а также все данные посетителя.

4. Программа (выгружается, если был отмечен чекбокс "НМО") — общий список пунктов программы (задаётся в "Титрах") и информация по ним.

5. НМО — список участников с подсчётом релевантных нажатий на кнопки подтверждения присутствия. Вместо заголовков лекции для краткости используются их ID, которые можно узнать из листа "Программа".

6. НМО (детализация) — общий список нажатий на кнопку подтверждения присутствия во всех залах по всем пользователям с указанием времени нажатия по Мск.

ВАЖНО: на листе "Участники" считается общее количество нажатий на кнопку подтверждения присутствия, а на листе "НМО" — количество релевантных! Соответственно, они могут не совпадать. Т.е. если нажатие на кнопку подтверждения присутствия произошло вне обязательной для НМО программы, то оно будет посчитано на листе "Участники", а на листе "НМО" — нет. Хотя, на листе "НМО (детализация)" оно тоже будет отображено, для полноты картины.

ТЕХНИЧЕСКАЯ ИНФОРМАЦИЯ
Захардкожены следующие ключи метаданных пользователей (значения ячеек meta_key в таблице БД usermeta), с которыми работает плагин:

* Surname — фамилия

* Name — имя

* LastName — отчество

* phone — номер телефона

* Date_of_Birth — дата рождения

* Organization — место работы

* Specialty — специальность

* town — город


Соответственно, при изменении ключа метаданных статистика перестанет собирать эти метаданные.

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

4. Учёт лекции в статистике по НМО — если не отмечено, то лекция не попадёт в лист "НМО" выгрузки статистики.

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

