<?php

namespace CurrencyBundle\Parser;

use CurrencyBundle\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class AbstractParser
 *
 * @package CurrencyBundle\Parser
 */
abstract class AbstractParser
{
    /** @var Registry */
    protected $doctrine;

    /** @var string */
    protected $bankUniqueId;

    /**
     * AbstractParser constructor.
     *
     * @param Registry $doctrine
     * @param string $bankUniqueId
     */
    public function __construct(Registry $doctrine, $bankUniqueId)
    {
        $this->doctrine = $doctrine;
        $this->bankUniqueId = $bankUniqueId;
    }

    /**
     * Execute content parser.
     */
    public function execute()
    {
        $parseData = $this->parseSource();
        foreach ($parseData as $course) {
            $this->insertCourse($course);
        }
    }

    /**
     * @param array $courseData
     */
    protected function insertCourse(array $courseData)
    {
        $bankRepository = $this->doctrine
                               ->getRepository('CurrencyBundle:Bank');
        $bank = $bankRepository->findOneBy(array('unique_id' => $this->bankUniqueId));

        $course = new Course();
        $course->setBank($bank);
        $course->setCurrency($courseData['currency']);
        $course->setCost($courseData['cost']);
        $course->setDate(new \DateTime());
        $course->setType(1);

        $em = $this->doctrine->getManager();

        // tells Doctrine you want to (eventually) save the Course (no queries yet)
        $em->persist($course);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
    }

    /**
     * @return array
     */
    abstract protected function parseSource();
}