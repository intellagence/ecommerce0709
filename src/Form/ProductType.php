<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Material;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*
            Dans les méthodes, on peut retrouver dans les parenthèses, des dépendances :
            syntaxe Class $object

            Dans l'object $builder on retrouve des méthodes add()
            Chacune va correspondre à un élément (child) du formulaire
            Si la class Form est rattachée à une class Entity
            les éléments du formulaire doivent correspondre aux propriétés de l'entity

            Il y a 3 arguments dans la méthode add()
            - 1e : (string) OBLIGATOIRE : nom de la propriété
            - 2e : ClassType::class : type de l'élément (textarea, select, input de type : text, number, password etc...)
            - 3e : (type array) tableau des options 
                    Il existe 2 categories d'options :
                        - les options communes à toutes les class (Type) : attr, label, required etc... 
                        - les options propres à la class (Type)

        */
            
        $builder

            ->add('title', null, [
                // key => value
                'label' => 'Titre du produit <span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success'
                ],
                'attr' => [
                    'placeholder' => 'Saisir le titre du produit',
                    'class' => 'borderForm'
                ],
                'help' => 'Le titre doit contenir entre 5 et 25 caractères',
                'help_attr' => [
                    'class' => 'text-info'
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'p-4 my-4 border border-dark rounded bg-light'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le titre du produit'
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 25,
                        'minMessage' => '5 caractères minimum',
                        'maxMessage' => '25 caractères maximum'
                    ])
                ]
            ])

            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix <span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success'
                ],
                'attr' => [
                    'placeholder' => 'Saisir le prix du produit',
                    'class' => 'borderForm'
                ],
                'help' => 'Le prix doit être strictement supérieur à <span class="text-danger">zéro</span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-info'
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'p-4 my-4 border border-dark rounded bg-light'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le prix du produit'
                    ]),
                    new Positive([
                        'message' => 'Veuillez saisir un prix strictement supérieur à zéro'
                    ])
                ]
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description <span class="text-warning">(Facultative)</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success'
                ],
                'attr' => [
                    'placeholder' => 'Saisir la description du produit',
                    'class' => 'borderForm',
                    'rows' => 4
                ],
                'help' => 'La description doit être inférieure à <span class="text-danger">200</span> caractères',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-info'
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'p-4 my-4 border border-dark rounded bg-light'
                ],
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => '200 caractères maximum'
                    ])
                ]
            ])

            ->add('category', EntityType::class, [ // EntityType : Recherche en BDD (relation)
                'class' => Category::class, // En bdd, dans quelle table récupérer
                'choice_label' => 'title', // Afficher dans le select la propriété title
                //'multiple' => true, UNIQUEMENT RELATION MANY (plusieurs choix) / par défaut false


                'label' => 'Catégorie<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'placeholder' => '-- Saisir la catégorie --',
                'expanded' => false, // false : balise select, true : radio/checkbox
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la catégorie du produit'
                    ])
                ]
            ])


            ->add('brand', EntityType::class, [ // EntityType : Recherche en BDD (relation)
                'class' => Brand::class, // En bdd, dans quelle table récupérer
                'choice_label' => 'title', // Afficher dans le select la propriété title
                //'multiple' => true, UNIQUEMENT RELATION MANY (plusieurs choix) / par défaut false


                'label' => 'Marque<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'placeholder' => '-- Saisir la marque --',
                'expanded' => false, // false : balise select, true : radio/checkbox
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la marque du produit'
                    ])
                ]
            ])

            ->add('materials', EntityType::class, [ // EntityType : Recherche en BDD (relation)
                'class' => Material::class, // En bdd, dans quelle table récupérer
                'choice_label' => 'title', // Afficher dans le select la propriété title
                'multiple' => true,

                'label' => 'Matière(s)<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'placeholder' => '-- Saisir au moins une matière --',
                'expanded' => true, // false : balise select, true : radio/checkbox
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez cocher au moins une matière'
                    ])
                ]
            ])

            ->add('picture', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/webp',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Uniquement des images, Extensions : WEBBP et PNG'
                    ])
                ]
            ])

            ->add('stock')

            // ->add('Enregistrer', SubmitType::class)
        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
