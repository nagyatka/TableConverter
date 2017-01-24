
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
$arrayDecoder = new ArrayCodec($testArray);

//If you use ArrayCodec as Coder you can leave the constructor blank.
$arrayCoder = new ArrayCodec();
```

**CsvCodec, XlsCodec, XlsxCodec**

```php

//If you use CsvCodec, XlsCodec or XlsxCodec as Decoder you have to specify the input file name.
$csvDecoder = new CsvCodec("test_file.csv");
$xlsDecoder = new XlsCodec("test_file.xls");
$xlsxDecoder = new XlsxCodec("test_file.xlsx");


//If you use CsvCodec, XlsCodec or XlsxCodec as Coder you have two options.

//First option: You specify the file name, thus the coder will write out the table in that file.
Converter::convert(new CsvCodec("test_file.csv"), new SimpleAssociationRule(), new XlsxCodec("new_test_file.xlsx"));

//Second option: If you don't set the filename parameter, the coder will return with the file as a string.
$xls = Converter::convert(new CsvCodec("test_file.csv"), new SimpleAssociationRule(), new XlsxCodec());

```

**MysqliCodec**

```php

//If you use MysqliCodec as Coder, you have to set the mysqli connection and the name of the table
$db = new mysqli('localhost', 'user', 'pass', 'demo');
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
$mysqliCoder = new MysqliCodec($db,"table_name");

//If you use MysqliCodec as Decoder, you have to more options. Firstly, you can use as same as in Coder. It will
//select the whole table with all column.
$mysqliDecoder = new MysqliCodec($db,"table_name");


//Another option is that you write an arbitrary sql query. In this case, you can leave the table name empty.
$mysqliDecoder = new MysqliCodec($db,"","SELECT * FROM table_name WHERE col1 > 5 OR col2 = 44");
$mysqliDecoder2 = new MysqliCodec($db,"","SELECT * FROM table_name JOIN table_name2 ON (table_name.id = table_name2.id)");


```


##AssociationRule



