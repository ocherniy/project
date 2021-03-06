<?php

namespace CurrencyBundle\Parser;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PrivatbankParser
 *
 * @package CurrencyBundle\Parser
 */
class PrivatbankParser extends AbstractParser
{

  const SOURCE_URL = 'https://privatbank.ua/';

  protected function parseSource()
  {
    $client = new Client();
    $response = $client->get(
      self::SOURCE_URL
    )->getBody();

    $data = array();
    if (!empty($response)) {
      $response = (string) $response;
      $crawler = new Crawler($response);

      $eur_buy = $crawler->filter('#selectByPB tr td')->eq(1)->html();
      $eur_sale = $crawler->filter('#selectByPB tr td')->eq(2)->html();
      $usd_buy = $crawler->filter('#selectByPB tr td')->eq(4)->html();
      $usd_sale = $crawler->filter('#selectByPB tr td')->eq(5)->html();

      if (!empty($usd_buy) && !empty($usd_sale)) {
        $data[] = array(
          'currency' => self::CURRENCY_USD,
          'cost_buy' => $usd_buy,
          'cost_sale' => $usd_sale,
          'type' => self::CURRENCY_TYPE_CASH,
        );
      }

      if (!empty($eur_buy) && !empty($eur_sale)) {
        $data[] = array(
          'currency' => self::CURRENCY_EUR,
          'cost_buy' => $eur_buy,
          'cost_sale' => $eur_sale,
          'type' => self::CURRENCY_TYPE_CASH,
        );
      }
    }

    return $data;
  }
}
