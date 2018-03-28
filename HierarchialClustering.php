<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 24.03.2018
 * Time: 23:40
 */
class HierarchialClustering
{
    private $dataSet;
    private $numOfClusters;

    private $filterWords = [];

    //Набор слов с атрибутами длины строки и расстояния до слова в предложении
    private $wordsWithAttributes = [];

    //содержит сгруппированные слова
    private $setOfWords = [];

    //Массив узлов
    private $arrayNodes = [];

    public function __construct(array $dataSet, int $numOfClusters)
    {
        $this->dataSet = $dataSet;
        $this->numOfClusters = $numOfClusters;
    }

    /*Разбивает входящие статьи на слова, фильтруем их(убираем слова длиной меньше 2 символов и слово the)
         подсчитываем кол-во встречающихся символов*/
    private function splitAndFilterArticle():void
    {
        foreach ($this->dataSet as $article) {
            $article = strtolower($article);
            $words = preg_split('#(\s+)|[.,!?]#', $article);
            foreach ($words as $word) {
                //Убираем встречающиеся цифры и слова длиной меньше 2
                if (strlen($word) <= 2 || preg_match('#(^\d+$)|(^[tT]he$)#', $word)) continue;
                if (!array_key_exists($word, $this->filterWords)) {
                    $this->filterWords[$word] = 0;
                }
                $this->filterWords[$word]++;
            }
        }
        //Удаляем слова, встречающиеся 1 раз в наборе
        $num = 0;
        foreach ($this->filterWords as $filterWord => $count) {
            if ($count == 1) {
                // unset($this->filterWords[$filterWord]);
                array_splice($this->filterWords, $num, 1);
                $num--;
            }
            $num++;
        }
        return;
    }

    //Получаем атрибуты для каждого слова: расстояние от начала строки до слова и длина слова
    private function getNodesAttributes():void
    {
        foreach ($this->dataSet as $article) {
            $article = strtolower($article);
            $words = preg_split('#(\s+)|[.,!?"]#', $article);
            //Начальное расстояние до нашего слова в строке
            $lengthToString = 0;
            foreach ($words as $word) {
                if (!array_key_exists($word, $this->filterWords)) {
                    //Если даное слово не вошло в набор, пропускам его и добавляем его длину в расстояние до искомого слова
                    $lengthToString += strlen($word);
                    continue;
                }
                $stringLength = strlen($word);
                $this->wordsWithAttributes[] = [$word => ['distance' => $lengthToString, 'strLength' => $stringLength]];
                //добавляяем в расстояние до след слова длину текущего слова
                $lengthToString += $stringLength;
            }
        }
        return;
    }

    //Нахождение медианы числового ряда
    private function calculateMediana(array $numArray):float
    {
        $mediana = 0;
        if (count($numArray) == 1) {
            $mediana = $numArray[0];
        } //Если массив имеет четное кол-во элементов, то считаем как среднее арифметическое средних элементов
        elseif (count($numArray) % 2 == 0) {
            $mediana = ($numArray[count($numArray) / 2] + $numArray[count($numArray) / 2 - 1]) / 2;
        } else {
            //берем средний элемент
            $mediana = $numArray[count($numArray) / 2];
        }
        return $mediana;
    }

    //Создаем узлы данных
    private function createNodes():void
    {
        foreach ($this->wordsWithAttributes as $wordWithAttributes) {
            foreach ($wordWithAttributes as $word => $attributes) {
                if (!array_key_exists($word, $this->setOfWords)) {
                    $this->setOfWords[$word] = [];
                    $this->setOfWords[$word]['distance'] = [];
                    $this->setOfWords[$word]['strLength'] = $attributes['strLength'];
                }
                //вставляем атррибут дистанции в массив
                array_push($this->setOfWords[$word]['distance'], $attributes['distance']);
                //сортируем дистанцию в порядке возрастания для нахождения медианы
                sort($this->setOfWords[$word]['distance']);
            }
        }
        foreach ($this->setOfWords as $word => $attributes) {
            foreach ($attributes as $name => $attribute) {
                if ($name == 'distance') {
                    $mediana = $this->calculateMediana($attribute);
                    //Создаем узлы
                    $this->arrayNodes[] = new Node($word, $mediana, $attributes['strLength']);
                }
            }
        }
        return;
    }

    public function cluster():array
    {
        //Разобъем статьи на слова и отфильтруем их
        $this->splitAndFilterArticle();
        //Определяем атрибуты(длина слова и расстояние от начала предложения до данного слова) экземпляров данных
        $this->getNodesAttributes();

        $this->createNodes();
        $count = count($this->setOfWords);
        //Создаем $numOfClusters кластеров
        while ($count > $this->numOfClusters) {
            $firstNode = $secondNode = null;
            $minDist = PHP_INT_MAX;

            //Находим min расстояние
            for ($i = 0; $i < count($this->arrayNodes) - 1; $i++) {
                for ($j = $i + 1; $j < count($this->arrayNodes); $j++) {
                    $dist = sqrt(pow($this->arrayNodes[$i]->getDistance() - $this->arrayNodes[$j]->getDistance(), 2) +
                        pow($this->arrayNodes[$i]->getStrLength() - $this->arrayNodes[$j]->getStrLength(), 2));
                    if ($minDist > $dist) {
                        $minDist = $dist;
                        $firstNode = &$this->arrayNodes[$i];
                        $secondNode = &$this->arrayNodes[$j];
                    }
                }
            }
            //merge nodes
            $newNodeWord = $firstNode->getWord() . ' - ' . $secondNode->getWord();
            $newDistance = ($firstNode->getDistance() + $secondNode->getDistance()) / 2;
            $newStrLength = ($firstNode->getStrLength() + $secondNode->getStrLength()) / 2;
            $newNode = new Node($newNodeWord, $newDistance, $newStrLength);
            $firstNode->setParent($newNode);
            $secondNode->setParent($newNode);
            $newNode->setLeft($firstNode);
            $newNode->setRight($secondNode);
            $this->arrayNodes[] = $newNode;
            $firstNode = null;
            $secondNode = null;
            //Удаляем 2 узла, которые объединяли
            for ($i = 0; $i < count($this->arrayNodes); $i++) {
                if (is_null($this->arrayNodes[$i])) {
                    unset($this->arrayNodes[$i]);
                }
            }
            $count--;
            //Присваиваем массиву значения заново, чтобы шли в порядке от 0 до count
            $this->arrayNodes = array_values($this->arrayNodes);
        }
        return $this->arrayNodes;

    }

    //Вывод содержимого кластеров
    public function bfs(Node $node):SplObjectStorage
    {
        $count = 1;
        $queue = new SplQueue();
        $values = new SplObjectStorage();
        $queue->enqueue($node);
        while (!$queue->isEmpty()) {
            $tempNode = $queue->dequeue();
            $s = str_repeat('-', $count);
            $values->attach($tempNode, $s);
            if ($tempNode->getLeft()) {
                $count++;
                $queue->enqueue($tempNode->getLeft());
            }
            if ($tempNode->getRight()) {
                $count++;
                $queue->enqueue($tempNode->getRight());
            }
        }
        return $values;
    }
}