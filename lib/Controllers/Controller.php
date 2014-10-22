<?php

$product = Dbproduct::getById($id);
Validator::isValid('product', $product);
Validator::isValidForUpdate('product', $product);

ProductHelper::save($product);
ProductHelper::isValid($product);
ProductHelper::isValidForUpdate($product);
DbProduct::save($product);
ProductHelper::isPopular($product);
