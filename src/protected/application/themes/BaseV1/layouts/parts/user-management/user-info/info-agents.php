<?php 
  use MapasCulturais\App;
  use MapasCulturais\Entities\Agent;
?>
  <table class="agents-table entity-table">
    <caption>
      <?=\MapasCulturais\i::_e("Agentes");?>
    </caption>
    <thead>
      <tr>
        <td>id</td>
        <td>Nome</td>
        <td>Subsite</td>
        <td>Operações</td>
      </tr>
    </thead>
    <tbody>
      <?php foreach($agents as $agent): ?>
      <tr>
        <td>
          <a class="icon icon-agent"></a>
          <a href="<?=$agent->singleUrl?>"><?=$agent->id?></a>
        </td>
        <td><?=$agent->name?></td>
        <td class="fit"><?=$agent->subsite?$agent->subsite->name:'';?></td>
        
        <td class="fit">

          <div class="entity-actions">
            <?php if(!$agent->isUserProfile): ?>
              <?php if($agent->status === Agent::STATUS_ENABLED): ?>
                <a class="btn btn-small btn-danger" href="<?php echo $agent->deleteUrl; ?>"><?php \MapasCulturais\i::_e("excluir");?></a>
                <a class="btn btn-small btn-success" href="<?php echo $agent->archiveUrl; ?>"><?php \MapasCulturais\i::_e("arquivar");?></a>

              <?php elseif ($agent->status === Agent::STATUS_DRAFT): ?>
                <a class="btn btn-small btn-warning" href="<?php echo $agent->publishUrl; ?>"><?php \MapasCulturais\i::_e("publicar");?></a>
                <a class="btn btn-small btn-danger" href="<?php echo $agent->deleteUrl; ?>"><?php \MapasCulturais\i::_e("excluir");?></a>

              <?php elseif ($agent->status === Agent::STATUS_ARCHIVED): ?>
                <a class="btn btn-small btn-success" href="<?php echo $agent->unarchiveUrl; ?>"><?php \MapasCulturais\i::_e("desarquivar");?></a>
              <?php elseif ($agent->status === Agent::STATUS_ARCHIVED): ?>
                <a class="btn btn-small btn-success" href="<?php echo $agent->unarchiveUrl; ?>"><?php \MapasCulturais\i::_e("desarquivar");?></a>
              <?php else: ?>
                <a class="btn btn-small btn-success" href="<?php echo $agent->undeleteUrl; ?>"><?php \MapasCulturais\i::_e("recuperar");?></a>
                <?php if($agent->canUser('destroy')): ?>
                  <a class="btn btn-small btn-danger" href="<?php echo $agent->destroyUrl; ?>"><?php \MapasCulturais\i::_e("excluir definitivamente");?></a>
                <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
          </div>

        </td>

      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

