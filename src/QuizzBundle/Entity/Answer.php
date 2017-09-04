<?php

namespace QuizzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="QuizzBundle\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Quizz", inversedBy="answer")
     * @ORM\JoinColumn(name="quizz_id", referencedColumnName="id")
     */
    private $quizz;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="string", length=255)
     */
    private $answer;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Answer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set quizz
     *
     * @param \QuizzBundle\Entity\Quizz $quizz
     *
     * @return Answer
     */
    public function setQuizz(\QuizzBundle\Entity\Quizz $quizz = null)
    {
        $this->quizz = $quizz;

        return $this;
    }

    /**
     * Get quizz
     *
     * @return \QuizzBundle\Entity\Quizz
     */
    public function getQuizz()
    {
        return $this->quizz;
    }

    public function __toString()
    {
        return $this->answer;
    }
}
