
Full informaition see here
https://docs.google.com/document/d/1YA-PBnmGSm2q3C6XsZxI2aWl0XosNX0YfL_seT2hDFU/edit?usp=sharing

Create the `approval_codes` module

Create an entity `approval_codes_entity` (`bundle_name`, `code`, `code_level`)

Сreate the module configuration form in three fields - a separator\delimiter, default site-level code, default contente-level code and CRUD to fill approval_codes_entity. All data is checked by regular expressions.

Create a custom service to get the code for the current page

Create a custom block where we use this service

Place via standart interface in any region block named _Approval codes_

Configure and edit codes via admin interface `/admin/config/system/approval-codes`


Almost all operations are performed using drush \ drupalconsole generators with little manual coding of logic.

