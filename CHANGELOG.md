CHANGELOG for 2.0.x
===================

2.0.0
------------------

 - BC Break: Upgrade minimum required PHP version to 7.2.
 - BC Break: Upgrade PHPUnit to 8.x.
 - BC Break: Use return types and type hinting in all classes.
 - BC Break: When a Bban doesn't support a method, return MethodNotSupportedException instead of empty string.
 - Feature: Add accountType getter for BbanInterface.
 - Feature: Add AbstractBban class which has bankCode and accoutNumber logic for most of Bbans. It also returns MethodNotSupportedException for the rest of the BbanInterface getters.
 - Feature: Add support for more countries Bbans:
    - Austria
    - Bulgaria
    - Belgium