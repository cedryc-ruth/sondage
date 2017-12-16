<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users view large-9 medium-8 columns content">
    <h3><?= h($userEntity->nickname) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($userEntity->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($userEntity->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('# Surveys') ?></th>
            <td><?= h($nbSurveys).' '.(($nbSurveys>1)?__(' sondages créés.'):__(' sondage créé.')) ?></td>
        </tr>
    </table>
</div>
<?php if(!empty($showUpdateForm)) { ?>
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($userEntity) ?>
    <fieldset>
        <legend><?= __('Edit User') ?></legend>
        <?php
            echo $this->Form->control('newPassword');
            echo $this->Form->control('confirmPassword');
            echo $this->Form->control('email');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
<?php } else { ?>
<div class="users form large-9 medium-8 columns content">
    <?= $this->Html->link('Modifier mes données','/users/profil/'.$user['id'].'?edit=1') ?>
    <form action="<?= $this->Url->build([
        '_name' => 'profil', 'id' => $userEntity->id,
        '?' => ['edit'=> $userEntity->id]]) ?>">
        <button type="submit"><?= __('Modifier mes données') ?></button>
    </form>
</div>
<?php } ?>