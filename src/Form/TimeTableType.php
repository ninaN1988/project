<?php

namespace App\Form;

use App\Entity\City;
use App\Repository\CityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeTableType extends AbstractType
{
    private $cityRepository;
	
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }
	
	public function buildForm(FormBuilderInterface $builder, array $options)
    {		
        $builder
            ->add('cityStart', EntityType::class, [
				'mapped' => false,
				'class'=>City::class,
				'choices' => $this->cityRepository->findAll(),
				'attr' => array('class' => 'selectpicker', 'data-live-search' => 'auto'),
			])
			->add('cityEnd', EntityType::class, [
				'class'=>City::class,
				'choices' => $this->cityRepository->findAll(),
				'attr' => array('class' => 'selectpicker', 'data-live-search' => 'auto'),
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //$resolver->setDefaults([
        //    'data_class' => Schedule::class,
        //]);
    }
}
