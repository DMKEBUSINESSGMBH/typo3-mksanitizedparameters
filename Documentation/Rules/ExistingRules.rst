.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _existingRules:

Existing Rules
==============

The list of current rules for parameters. You can overwrite this.

Backend
-------

- id (common)
   - String
- uid (common)
   - String
- pid (common)
   - String


Frontend
--------
- id (common)
   - String
- uid (common)
   - Integer
- pid (common)
   - String
- AMEOSFORMIDABLE_ADDPOSTVARS (common)
   - Raw
- __hmac (common)
   - Raw
- default (all unconfigured parameters)
   - String
   - Magic Quotes

- mksearch
   - mksearch[term]
      - Raw


- caretaker (_instance)
   - st
      - String
   - d
      - Raw
   - s
      - Raw


- fluid_recommendation
   - tx_fluidrecommendation_pi1[url]
      - URL
   - tx_fluidrecommendation_pi1[__referrer][extensionName]
      - String
   - tx_fluidrecommendation_pi1[__referrer][controllerName]
      - String
   - tx_fluidrecommendation_pi1[__referrer][actionName]
      - String
   - tx_fluidrecommendation_pi1[recommendation][receiverLastName]
      - String
   - tx_fluidrecommendation_pi1[recommendation][receiverMail]
      - Email
   - tx_fluidrecommendation_pi1[recommendation][senderLastName]
      - String
   - tx_fluidrecommendation_pi1[recommendation][senderMail]
      - Email
   - tx_fluidrecommendation_pi1[recommendation][message]
      - String
   - tx_fluidrecommendation_pi1[recommendation][url]
      - URL


- powermail
   - tx_powermail_pi1[url]
      - URL
   - tx_powermail_pi1[__referrer][extensionName]
      - String
   - tx_powermail_pi1[__referrer][controllerName]
      - String
   - tx_powermail_pi1[__referrer][actionName]
      - String
   - tx_powermail_pi1[form]
      - Integer
   - tx_powermail_pi1[field]*
      - Raw