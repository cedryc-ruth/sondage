<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Survey Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $question
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Response[] $responses
 */
class Survey extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'question' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'responses' => true
    ];
    
    public function computePourcentage() {
        $total = 0;
        foreach($this->responses as $response) {
            $total += $response->count;
        }
        
        return $total;
    }
}
