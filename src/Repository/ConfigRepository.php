<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 04.01.18
 * Time: 17:03
 */

namespace App\Repository;


use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;

class ConfigRepository
{
    public const LOCALE = "locale";
    public const CURRENCY = "currency";
    public const EXCHANGE = "exchange";
    public const INVOICE_TIMEOUT = "invoice_timeout";
    private $repository;
    private $em;

    /**
     * ConfigRepository constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Config::class);
    }


    public function getConfig(string $key, string $default = ""): Config
    {
        $config = $this->repository->find($key);
        if (!$config)
            $config = new Config($key, $default);

        if ($this->em->isOpen())
            $this->em->persist($config);

        return $config;
    }
}