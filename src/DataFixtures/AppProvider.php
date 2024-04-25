<?php

namespace App\DataFixtures;

use DateTime;

$now= new DateTime('now');


class AppProvider
{
// Embroideries properties
    private $embroideryName = [
        'Tapisserie Royale',
        'Broderie Élégance',
        'Esquisse Enchantée',
        'Charme de la Broderie',
        'Couture Couronnée',
        'Fil de la Noblesse',
        'Broderie Impériale',
        'Délice Dentelle',
    ];

    private $embroideryDesign = [
        'Chaton',
        'Chiot',
        'Souris',
        'Ballon',
        'Coeur',
        'Etoile',
        'Fleur',
        'Papillon',
        'Feuille',
        'Lune',
        'Ancre',
        'Nuage',
        'Fusée',
        'Eclair',
    ];

    private $embroideryDetail = [
        'Fil métallisé argenté, coton noir, position centrée en haut à gauche.',
        'Coton bleu marine, tissu blanc cassé, position centrée en bas à droite.',
        'Fil brillant argenté, tissu bleu foncé, position centrée en haut à droite.',
        'Coton vert clair, lin beige, position centrée en bas à gauche.',
        'Laine bleu, velours rouge, position centrée à droite.',
        'Polyester vert, coton blanc, position diagonale en haut à gauche.',
        'Fil métallisé doré, velours noir, position centrée en bas.',
        'Soie rose, satin blanc, position centrée au centre.',
        'Coton rouge, lin beige, position centrée en haut.',
        'Soie rouge, coton blanc, position centrée en haut à gauche.',
        'Fil de laine marron, tissu beige, position centrée en bas.',
        'Polyester jaune, coton blanc, position centrée à droite.',
        'Coton multicolore, lin beige, position centrée en haut à droite.',
        'Coton bleu, tissu blanc cassé, position centrée en bas à gauche.',
    ];
    
    private $embroideryText = [
        'Happy New Year',
        'Train Journey',
        'Tea Party',
        'Christmas Story',
        'Dark Chocolate',
        'Childrens Party',
        'Sunny Morning',
        'Zen Garden',
        'Mountain Trip',
        'Outdoor Cinema',
        'Music Festival',
    ];

// Textile properties

    private $textileName = [
        'JO forever blue',
        'Just Married pink',
        'Free Hugs yellow',
        'USA NYC soccer team ',
        'Sail Away',
        'Flash',
        'Nature Lover',
        'Dream Big',
        'Fly High',
        'I Love Bordeaux',
        'Moonlight',
    ];

    private $textileType = [
        'Casquette',
        'Tee-shirt',
        'Serviette',
        'Torchon',
        'Short',
        'Chemise',
        'Caleçon',
        'Barboteuse',
        'Jupe',
        'Pantalon',
        'Chaussette',
    ];

    private $textileSize = [
        'S',
        'M',
        'L',
        'XL',
        'XXL',
        'XXXL',
    ];

    private $textileColor = [
        'Blanc',
        'Bleu',
        'Noir',
        'Rose',
        'Vert',
        'Jaune',
        'Violet',
        'Orange',
        'Rouge',
    ];

    private $textileBrand = [
        'Nike',
        'Adidas',
        'Damart',
        'Pimkie',
        'Celio',
        'Uniqlo',
        'Kiabi',
        'Bexley',
    ];

    private $textileComment = [
        'Difficile à coudre',
        'Déteint à plus de 40]',
        'Rétrécit',
        'Ne pas repasser',
        'Laine épaisse',
        'Lin fin',
        'Tissage serré',
    ];

// Customer properties

    private $customerName = [
        'Association équinoxe',
        'Fayis Martin',
        'Laurent Dirac',
        'Théophile Hawking',
        'Laure Cantor'
    ] ;

    private $customerAddress = [
        '1 boulevard de la république 34000 Montpellier',
        '21 cours de alsace lorraine 33000 Bordeaux',
        '145 impasse montaigne 13000 Marseille',
        '18 rue des prés verts 78220 Viroflay',
        '7 rue Montesquieu 59000 Lille'
    ] ;

    private $customerEmail = [
        'eva@free.fr',
        'fayis@free.fr',
        'laurent@free.fr',
        'theophile@free.fr',
        'laure@free.fr'
    ] ;

    private $customerContact = [
        'Mrs Chen',
        'Mr Show',
        'Miss Lam',
        'Mr Fong',
        'Mrs Tam'
    ] ;

    private $customerPhoneNumber = [
        '0143567564',
        '0678956723',
        '0567453412',
        '0952345423',
        '0832123423'
    ] ;

// Contract properties

    private $contractType = [
        'quotation',
        'order',
        'invoice',
    ] ;

    private $contractDeliveryAddress = [
        'rés rossini appt211 bat b 34 rue abel antoune 33000 bordeaux',
        '2 rue de la fontaine 66000 Perpignan ',
        '67 av de la mairie 31000 Toulouse',
        '89 route de la forêt 69001 Lyon',
        '65 chemin de traverse 14000 Caen',
    ] ;

    private $contractStatus = [
        'created',
        'archived',
        'obsolete',
        'deleted'
    ] ;

    private $contractComment = [
        'Je veux un chiot dessiné sur la casquette',
        'Expédié en bateau',
        'Ne pas joindre la facture au colis',
        'Cadeau pour ma femme',
    ] ;

// User properties

// private $userPseudo = [
//     'kuro',
//     'lor'
// ] ;
// private $userPassword = [
//     '$2y$13$3FZqOJQ8VOtAvjNrg8FOY.wvEJgzsRta2niyi654KMWh2FcYMMSFy',
//     '$2y$13$GL.xtZfB1ana9rFittdBWerUEgspWwaiU7IYQaf5qi/swjG9MaAM.'
// ] ;
// private $userRole = [
//     'ROLE_ADMIN',
//     'ROLE_USER'
// ] ;

// Product properties

    private $productName = [
        'Casquette jo 2024',
        'Tee shirt fleuri paillettes',
        'Enterrement de vie de jeune fille',
        'Goodies de la société équinoxe',
        'Cadeaux du mariage des Dupont',
        
    ] ;
    private $productComment = [
        'Très fragile',
        'Laver à 30 degrés',
        'Broderie à faire uniquement à la main',
        'Produit rapide à faire ',
        'Quantité limité',
    ] ;


//ne pas faire les dates, ne pas faire les codes (clé étrangère)


    public function getEmbroideryName()
    {
        return $this->embroideryName;
    }

    public function getEmbroideryDesign()
    {
        return $this->embroideryDesign;
    }
    public function getEmbroideryDetail()
    {
        return $this->embroideryDetail;
    }

    public function getEmbroideryText()
    {
        return $this->embroideryText;
    }

    public function getTextileName()
    {
        return $this->textileName;
    }

    public function getTextileType()
    {
        return $this->textileType;
    }

    public function getTextileColor()
    {
        return $this->textileColor;
    }

    public function getTextileSize()
    {
        return $this->textileSize;
    }

    public function getTextileBrand()
    {
        return $this->textileBrand;
    }

    public function getTextileComment()
    {
        return $this->textileComment;
    }

    public function getCustomerName()
    {
        return $this->customerName;
    }

    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    public function getCustomerContact()
    {
        return $this->customerContact;
    }

    public function getCustomerPhoneNumber()
    {
        return $this->customerPhoneNumber;
    }
    public function getContractType()
    {
        return $this->contractType;
    }

    public function getContractDeliveryAddress()
    {
        return $this->contractDeliveryAddress;
    }

    public function getContractStatus()
    {
        return $this->contractStatus;
    }

    public function getContractComment()
    {
        return $this->contractComment;
    }

    public function getUserPseudo()
    {
        return $this->userPseudo;
    }

    public function getUserPassword()
    {
        return $this->userPassword;
    }

    public function getUserRole()
    {
        return $this->userRole;
    }

    /**
     * Get the value of productName
     */ 
    public function getProductName()
    {
    return $this->productName;
    }

    /**
     * Get the value of productComment
     */ 
    public function getProductComment()
    {
    return $this->productComment;
    }
    
}

   
    


// TODO fixture de la updated date
