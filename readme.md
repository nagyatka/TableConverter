
# User documentation


To convert a table to another format you have to define 3 parameters:

* A decoder which tries to decode the original format
* A coder which will generate the new table
* An association rule which helps the converter to make an appropriate conversion

```php
Converter::convert(Decoder $decoder, AssociationRule $associationRule, Coder $coder);
```

##Simple examples 

It inserts an xlsx file to mysql table using mysqli. It will use the xls header as column names of the mysql table:

```php
Converter::convert(new XlsxCodec("test_file.xlsx"), new SimpleAssociationRule(), new MysqliCodec($connection,"table_name"));
```

It inserts an php array to mysql table using mysqli. It will use the array keys as column names of the mysql table:
```php
$testArray = [
    ["col1" => 2, "col2" => 3, "col3" => "yes"],
    ["col1" => 1, "col2" => 222, "col3" => "apple"],
];
Converter::convert(new ArrayCodec($testArray), new SimpleAssociationRule(), new MysqliCodec($connection,"table_name"));
```

##Codecs

Codec classes contain the coder and decoder implementation for an appropriate format.

**ArrayCodec**

```php

$testArray = [
    ["col1" => 2, "col2" => 3, "col3" => "yes"],
    ["col1" => 1, "col2" => 222, "col3" => "apple"],
];

//If you use ArrayCodec as Decoder you have to specify the input associative array.
$arrayCodec = new ArrayCodec($testArray);

//If you use ArrayCodec as Coder you can leave the constructor blank.
$arrayCodec = new ArrayCodec();
```

**CsvCodec, XlsCodec, XlsxCodec**

```php

//If you use CsvCodec, XlsCodec or XlsxCodec as Decoder you have to specify the input file name.
$csvCodec = new CsvCodec("test_file.csv");
$xlsCodec = new XlsCodec("test_file.xls");
$xlsxCodec = new XlsxCodec("test_file.xlsx");


//If you use CsvCodec, XlsCodec or XlsxCodec as Coder you have two options.

//First option: You specify the file name, thus the coder will write out the table in that file.
$xlsCodec = new XlsCodec("test_file.xls");

//Second option: You leave the para

```

**MysqliCodec**


##AssociationRule



