<?php
/***************************************************************
* Можно динамически и удобно расширять функционал "на будущее". Рядом с 'type' дерева в JSON сада можно добавить доп параметры каждого дерева.
* Например: Возраст, состояние, и тд. Что будет влиять на плоды
* Скрипт полностью динамический, можно лишь в JSON добавить новый вид деревьев, и он начнет полноценно работать
***************************************************************/

class GardenClass
{
	public function loadGarden()
	{
		$this->Garden = json_decode(file_get_contents('garden.json'), true);
		if(!count($this->Garden['Tree']))return "В саду нет деревьев :(\n";
		else return "В нашем саду растёт " . count($this->Garden['Tree']) . " деревьев!\n";
	}
	
	public function Verify()
	{
		$keys = array_keys($this->Garden['Settings']);
		for($x = 0;$x < count($keys);$x++){
			if(!isset($this->Garden['Settings'][$keys[$x]]['name'])){echo 'Отсутствует name для ' . $keys[$x] . "\n"; exit;}
			if(!isset($this->Garden['Settings'][$keys[$x]]['item']['max'])){echo 'Отсутствует item max для ' . $keys[$x] . "\n"; exit;}
			if(!isset($this->Garden['Settings'][$keys[$x]]['item']['min'])){echo 'Отсутствует item min для ' . $keys[$x] . "\n"; exit;}
			if(!isset($this->Garden['Settings'][$keys[$x]]['weight']['min'])){echo 'Отсутствует weight min для ' . $keys[$x] . "\n"; exit;}
			if(!isset($this->Garden['Settings'][$keys[$x]]['weight']['min'])){echo 'Отсутствует weight min для ' . $keys[$x] . "\n"; exit;}
		}
	}
	
	public function GetItems()
	{
		for($x = 0;$x < count($this->Garden['Tree']);$x++){
			// Количество плодов вычисляется из Settings для каждого дерева отельно
			$item = rand($this->Garden['Settings'][$this->Garden['Tree'][$x]['type']]['item']['min'], $this->Garden['Settings'][$this->Garden['Tree'][$x]['type']]['item']['max']);
			
			// Для интереса сделаем реально: У каждого плода свой вес
			for($z = 0; $z < $item;$z++){ $weight = rand($this->Garden['Settings'][$this->Garden['Tree'][$x]['type']]['weight']['min'], $this->Garden['Settings'][$this->Garden['Tree'][$x]['type']]['weight']['max']); }
			
			$db['Collector'][$this->Garden['Tree'][$x]['type']]['item'] = ($db['Collector'][$this->Garden['Tree'][$x]['type']]['item'] ?? 0) + $item;
			$db['Collector'][$this->Garden['Tree'][$x]['type']]['weight'] = ($db['Collector'][$this->Garden['Tree'][$x]['type']]['weight'] ?? 0) + $weight;
			$db['total']['item'] = ($db['total']['item'] ?? 0) + $item;
			$db['total']['weight'] = ($db['total']['weight'] ?? 0) + $weight;
			echo "Дерево " . ($x+1) . " - " . $this->Garden['Settings'][$this->Garden['Tree'][$x]['type']]['name'] . ": Плодов собрано: " . $item . " шт. | Общий вес плодов: " . $weight . " гр.\n";
		}
		
		echo "-------------------------------------------------\n";
		echo "Сбор плодов завершён! Вот что пополнит наш склад:\n";
		$keys = array_keys($db['Collector']);
		for($x = 0;$x < count($keys);$x++){
			echo "- " . $this->Garden['Settings'][$keys[$x]]['name'] . ":\n";
			echo ">> Плодов: " . $db['Collector'][$keys[$x]]['item'] . " шт.\n";
			echo ">> Вес плодов: " . $db['Collector'][$keys[$x]]['weight'] . " гр.\n";
		}
		echo "->> Общее количество плодов: " . $db['total']['item'] . " шт.\n";
		echo "->> Общий вес плодов: " . $db['total']['weight'] . " гр. (" . round($db['total']['weight']/1000, 1) . " кг.)\n";
	}
}

$Garden = new GardenClass();
echo $Garden->loadGarden(); // Тут загружаем. Если я правильно понял по ТЗ, то загружать нужно отдельно.
$Garden->Verify(); // Проводим тесты на верные данные
$Garden->GetItems(); // Начинаем саму сборку
?>