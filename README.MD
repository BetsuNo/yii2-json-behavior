# Yii2 JSON behavior

## Installation
Run command in shell
```shell
> php composer.phar require "betsuno/yii2-json-behavior"
```
or add
```json
"betsuno/yii2-json-behavior": "*"
```
in the `require` section of the `composer.json` file in your project 

## Usage

```php
use yii2/behaviors/JsonBehavior
...
class YourClass extendes Model
{
    ...
    public function behaviors()
    {
        return [
            JsonBehavior::register('propertyName'),
        ];
    }
    ...
}
```