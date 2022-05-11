<?php
declare(strict_types=1);

final class CarParser
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getCarList(int $page = 1): array
    {
        $carList = [];

        for ($i = 1; $i <= $page; $i++) {
            try{
                $carData = $this->parse($this->getPage($i));
            } catch (LimitRequestExceededException){
                return \array_merge([], ...$carList);
            }

            $carList[] = $carData;
        }

        return \array_merge([], ...$carList);
    }

    private function getPage(int $page): string
    {
        $maxTries = 10;

        for ($tries = 0; $tries < $maxTries; $tries++) {
            try {
               return $this->client->request($page);
            } catch (\Throwable) {
                \sleep(1);
            }
        }

        throw new LimitRequestExceededException($maxTries);
    }

    private function parse(string $data): array
    {
        $doc = new DOMDocument();
        $doc->validateOnParse = true;
        $doc->loadHTML($data, LIBXML_NOERROR);

        $xpath = new DOMXPath($doc);

        $carNames = $xpath->query("//div[@class='item ticket-title']/a[@data-template-v='6']");
        $carProperty = $xpath->query("//ul[@class='unstyle characteristic']");
        $carLink = $xpath->query("//div[@class='item ticket-title']/a/@href");

        $t = static fn(\DOMNode $item) => \trim($item->textContent);
        $carList = [];
        for ($num = 0; $num < $carNames->length; $num++) {
            $carProp = $carProperty->item($num + 1)->childNodes;

            $id = \trim(\strrchr($t($carLink->item($num + 1)), "/"), '/');
            $name = $t($carNames->item($num)->childNodes->item(1));
            $year = $t($carNames->item($num)->childNodes->item(2));
            $transmission = \implode(" ", \preg_split('/\s{43,}/', $t($carProp->item(7))));
            $carMileage = \implode(" ", \preg_split('/\s{43,}/', $t($carProp->item(1))));
            $fuel = \implode(" ", \preg_split('/\s{43,}/', $t($carProp->item(5))));
            $carList[] = new Car($id, $name, $year, $carMileage, $fuel, $transmission);
        }

        return $carList;
    }


}