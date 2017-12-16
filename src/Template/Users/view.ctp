<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<?php //var_dump($user); ?>
<div class="users view large-9 medium-8 columns content">
    <h3><?= h($user['nickname']) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($user['id']) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($user['email']) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('# Surveys') ?></th>
            <td><?= h($nbSurveys).' '.(($nbSurveys>1)?__(' sondages créés.'):__(' sondage créé.')) ?></td>
        </tr>
    </table>
</div>
