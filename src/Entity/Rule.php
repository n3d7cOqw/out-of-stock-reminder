<?php

namespace OutOfStockReminder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @ORM\Entity
 * @ORM\Table(name="ps_out_of_stock_rules")
 */

class Rule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $product_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $category_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $threshold;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;
    /**
     * @ORM\Column(type="text")
     */
    private $email;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getProductId(){
        return $this->product_id;
    }

    public function setProductId($productId){
        $this->product_id = $productId;
    }

    public function getCategoryId(){
        return $this->category_id;
    }

    public function setCategoryId($category_id){
        $this->category_id = $category_id;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }
    public function getThreshold(){
        return $this->threshold;
    }

    public function setThreshold($threshold){
        $this->threshold = $threshold;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }

}