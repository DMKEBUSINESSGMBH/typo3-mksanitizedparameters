.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction:

Introduction
============


.. _what-it-does:

What does it do?
----------------

Sanitizes all parameters in $_GET, $_POST und $_COOKIE. Every possible parameter
can be configured separately. The configuration can be for a specific position
in the parameter array or common for every possible position or even default for all
parameters, which are not configured.

The sanitizing itself is done through the filter function of PHP. So you can take full advantage
of it's features.


.. _features:

Features
--------
The extension has 3 modes which can be configured through extension configuration:

- stealth mode: simulate the sanitizing and log all theoretical actions. you also need to set a page id where the logs are written to.

- log mode: every parameter which is sanitized (has changed) will be logged at warn level. This way you can investigate what happened. Either it was an attack attempt or the rules have to be adjusted.

- debug mode: useful during development. every parameter which is sanitized (has changed) will be reported on the screen through a debug message.

.. _rules:

Rules
-----

Own rules for sanitizing a parameter can be registered easily. see :ref:`rules`