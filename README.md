# :construction: WIP Components Addon für REDAXO 5

Component im Template/Modul:

```html
<rex-alert type="danger" message="Oh no!"/>
```

Für die Ausgabe muss ein Fragment angelegt werden in _fragments/components_.

```php
<?php
$styles = 'max-w-6xl mx-auto rounded-lg py-5 px-6 mb-3 text-base inline-flex items-center w-full ';

switch ($this->type) {
    case 'danger':
        $styles .= 'bg-red-100 text-red-700';
        break;
    case 'warning':
        $styles .= 'bg-yellow-100 text-yellow-700';
        break;
    default:
        $styles .= 'bg-blue-100 text-blue-700';
        break;
}
?>

<div class="flex">
    <div class="<?= $styles ?>" role="alert">
        <?= $this->message ?>
    </div>
</div>
```

## TODO

- [ ] Cache statt Outputfilter nutzen
- [ ] Slots nutzen

## Credits

**Laravel**
https://github.com/laravel/laravel

**Torch**
https://github.com/mattstauffer/Torch
