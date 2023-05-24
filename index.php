<?php
mb_internal_encoding('UTF-8');


class ProductList
{
    private string $filename;
    private $products;

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->products = $this->loadProducts();
    }

    private function loadProducts()
    {
        if (!empty($this->filename) && file_exists($this->filename)) {
            $lines = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $products = [];

            foreach ($lines as $line) {
                $data = explode(' — ', $line);
                $id = $data[0];
                $name = $data[1];
                $price = $data[2] ?? 0;
                $products[$id] = ['name' => $name, 'price' => $price];
            }
            return $products;

        } else {
            echo "Товар '$this->filename' не найден! хотите создать ?" . PHP_EOL;
            echo "Выберите действие:" . PHP_EOL;
            echo "1 - Да" . PHP_EOL;
            echo "2 - Нет" . PHP_EOL;
            $choice = trim(fgets(STDIN));
            switch ($choice) {
                case '1':
                    $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

                    if ($extension === 'txt') {
                        if (touch($this->filename)) {
                            echo "Товар '$this->filename' успешно создан.";
                        } else {
                            echo "Не удалось создать '$this->filename'.";
                        }
                    } else {
                        echo "Расширение файла должно быть '.txt'.";
                    }

                    break;
                case '2':
                    exit();
                    break;
            }


            exit();
        }
    }

    public function saveProducts()
    {
        $lines = [];

        foreach ($this->products as $id => $product) {
            $line = $id . ' — ' . $product['name'] . ' — ' . $product['price'];
            $lines[] = $line;
        }

        file_put_contents($this->filename, implode(PHP_EOL, $lines));
    }

    public function addProduct()
    {
        $lastId = !empty($this->products) ? max(array_keys($this->products)) : 0;
        $newId = (int)$lastId + 1;



        echo "Введите наименование продукта: ";
        $name = trim(fgets(STDIN));

        echo "Введите цену продукта: ";
        $price = trim(fgets(STDIN));

        $this->products[$newId] = ['name' => $name, 'price' => $price];
        $this->saveProducts();
        echo "Продукт '$name' с ценой '$price' был добавлен. ID: $newId" . PHP_EOL;
    }

    public function updateProduct()
    {
        echo "Введите ID продукта для изменения: ";
        $id = trim(fgets(STDIN));

        if (isset($this->products[$id])) {
            echo "Текущее наименование продукта: " . $this->products[$id]['name'] . PHP_EOL;
            echo "Текущая цена продукта: " . $this->products[$id]['price'] . PHP_EOL;

            echo "Введите новое наименование продукта: ";
            $name = trim(fgets(STDIN));

            echo "Введите новую цену продукта: ";
            $price = trim(fgets(STDIN));

            $this->products[$id]['name'] = $name;
            $this->products[$id]['price'] = $price;
            $this->saveProducts();
            echo "Продукт с ID '$id' был обновлен. Новое наименование: '$name', Новая цена: '$price'" . PHP_EOL;
        } else {
            echo "Продукт с ID '$id' не найден." . PHP_EOL;
        }
    }

    public function deleteProduct()
    {
        echo "Введите ID продукта для удаления: ";
        $id = trim(fgets(STDIN));

        if (isset($this->products[$id])) {
            $name = $this->products[$id]['name'];
            unset($this->products[$id]);
            $this->saveProducts();
            echo "Продукт с ID '$id' и наименованием '$name' был удален." . PHP_EOL;
        } else {
            echo "Продукт с ID '$id' не найден." . PHP_EOL;
        }
    }

    public function subtractTotalPrice()
    {
        $totalPrice = 0;

        foreach ($this->products as $id => $product) {
            $totalPrice += $product['price'];
        }

        echo "Итог: $totalPrice" . PHP_EOL;
    }
    public function displayProducts()
    {
        echo "Список товаров:" . PHP_EOL;

        foreach ($this->products as $index => $product) {
            echo "ID: " . ($index + 1) . PHP_EOL;
            echo "Имя: " . $product['name'] . PHP_EOL;
            echo "Цена: " . $product['price'] . PHP_EOL;
            echo PHP_EOL;
        }
    }
    public function run()
    {

        while (true) {

            echo "Выберите действие:" . PHP_EOL;
            echo "1 - Добавить в товар" . PHP_EOL;
            echo "2 - Изменить запись в списке" . PHP_EOL;
            echo "3 - Удалить из списка" . PHP_EOL;
            echo "4 - Вывести все продукты" . PHP_EOL;
            echo "5 - Вычесть общую сумму" . PHP_EOL;
            echo "0 - Выход" . PHP_EOL;

            // Prompt for user input
            echo "Введите номер действия: ";
            $choice = trim(fgets(STDIN));

            // Process user choice
            switch ($choice) {
                case '1':
                    $this->addProduct();
                    break;
                case '2':
                    $this->updateProduct();
                    break;
                case '3':
                    $this->deleteProduct();
                    break;
                case '4':
                    $this->displayProducts();
                    break;
                case '5':
                    $this->subtractTotalPrice();
                    break;
                case '0':
                    echo "Программа завершена." . PHP_EOL;
                    return;
                default:
                    echo "Некорректный выбор действия." . PHP_EOL;
                    break;
            }
        }
    }

}



$filename = $argv[1] ?? '';


if (!$filename) {
    echo "Необходимо указать имя файла." . PHP_EOL;
    exit();
}
$productList = new ProductList($filename);

$productList->run();
