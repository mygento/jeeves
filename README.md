# jeeves
Code generator M2


install

go to ```https://github.com/mygento/jeeves/releases/latest``` download phar and then
```
sudo mv jeeves.phar /usr/local/bin/jeeves
```


sample

look in ```.jeeves.phpunit_v1.yaml``` or ```.jeeves.phpunit_v0.yaml```



## Yaml Schema

1. Root Level

| Property | Description | Example |
|--|--|--|
| settings | Global ```Settings```  |
| hash, [a-Z] | Vendor Name: [Module] | Mygento


2. Module Level

| Property | Description |
|--|--|
| settings | Module ```Settings``` |
| shipping | Module ```Shipping``` |
| entities | Module ```Entity```  List |


3. Entity Level

| Property | Description | Required | Default |
| -- | -- | -- | -- |
| settings | Entity ```Settings``` | N
| columns | Entity ```Columns``` | Y
| indexes | Entity ```Indexes``` | N
| fk | Entity ```Fk``` | N
| tablename | String | N | %vendor%_%module%_%entity%
| comment | String | N
| api | Boolean | N | false
| cacheable | Boolean | N | false
| cache_tag | String | N
| per_store | Boolean | N | false


4. Columns Level

| Property | Description | Required | Default | Comment |
| -- | -- | -- | -- | -- |
| type | String | Y |
| pk | Boolean | N | false |
| identity | Boolean | N | false | Auto Increment
| unsigned | Boolean | N | false |
| comment | String | N |
| nullable | Boolean | N | true |
| length | Integer | N |
| default | String | N |
| on_update | Boolean | N | false |

5. Indexes Level

| Property | Description | Required |
| -- | -- | -- |
| type | String | Y |
| columns | String[] | Y |

6. Fk Level

| Property | Description | Required |
| -- | -- | -- |
| column | String | Y |
| referenceTable | String | Y |
| referenceColumn | String | Y |
| indexName | String | Y |

7. Settings level

| Property | Description | Required | Default | Comment |
| -- | -- | -- | -- | -- |
| php_version | String | N | PHP_VERSION
| admin_route | String | N | %module%
| version | string | N | 2.4 | Magento version