<?php
$action = preg_replace("#^(\w+/)#", "", $this->template);

if (is_editable()) {
    add_entity_types_to_js($entity);
    add_taxonoy_terms_to_js('tag');
    add_taxonoy_terms_to_js('linguagem');

    add_entity_properties_metadata_to_js($entity);

    $app->enqueueScript('app', 'events', '/js/events.js', array('mapasculturais'));
    $app->enqueueScript('vendor', 'jquery-ui-datepicker', '/vendor/jquery-ui.datepicker.js', array('jquery'));
    $app->enqueueScript('vendor', 'jquery-ui-datepicker-pt-BR', '/vendor/jquery-ui.datepicker-pt-BR.min.js', array('jquery'));
    //$app->enqueueStyle('vendor', 'jquery-ui-datepicker', '/vendor/jquery-ui.datepicker.min.css');
}

add_map_assets();

add_occurrence_frequencies_to_js();
?>
<script> $(function(){ MapasCulturais.Map.initialize({mapSelector:'.js-map', isMapEditable:false}); }); </script>

<?php ob_start(); /* Event Occurrence Item Template - Mustache */ ?>
    <div id="event-occurrence-{{id}}" class="regra clearfix" data-item-id="{{id}}">
        <header class="clearfix">
            <h3 class="alignleft"><a href="{{space.singleUrl}}">{{space.name}}</a></h3>
            <a class="toggle-mapa" href="#"><span class="ver-mapa">ver mapa</span><span class="ocultar-mapa">ocultar mapa</span> <span class="icone icon_pin"></span></a>
        </header>
        <div class="infos">
            <!--p class="label">Resumo da regra que será exibido pro público.</p-->
            <p><span class="label">Horário inicial:</span> {{rule.startsAt}}</p>
            <p><span class="label">Horário final:</span> {{rule.endsAt}}</p>
            <?php if(is_editable()): ?>
                <p class="privado"><span class="icone icon_lock"></span><span class="label">Frequência:</span> {{rule.screen_frequency}}</p>
            <?php endif; ?>
            <p><span class="label">Data inicial:</span> {{rule.screen_startsOn}}</p>
            {{#rule.screen_until}}<p><span class="label">Data final:</span> {{rule.screen_until}}</p><!--(Se repetir mostra o campo de data final)-->{{/rule.screen_until}}
        </div>
        <!-- .infos -->
        <div id="occurrence-map-{{id}}" class="mapa js-map" data-lat="{{space.location.latitude}}" data-lng="{{space.location.longitude}}"></div>
        <!-- .mapa -->
        <?php if(is_editable()): ?>
            <div class="clear">
                <a class="editar botao js-open-dialog hltip"
                   data-dialog="#dialog-event-occurrence"
                   data-dialog-callback="MapasCulturais.eventOccurrenceUpdateDialog"
                   data-dialog-title="Modificar Ocorrência"
                   data-form-action="edit"
                   data-item="{{serialized}}"
                   href="#" title='Editar Ocorrência'>editar</a>
               <a class='excluir botao js-event-occurrence-item-delete hltip' data-href="{{deleteUrl}}" data-target="#event-occurrence-{{id}}" data-confirm-message="Excluir esta Ocorrência?" title='Excluir Ocorrência'>excluir</a>
            </div>
        <?php endif; ?>
    </div>
<?php $eventOccurrenceItemTemplate = ob_get_clean(); ?>


<?php ob_start(); /* Event Occurrence Item Template VIEW - Mustache */ ?>
    <div id="event-occurrence-{{id}}" class="regra clearfix" data-item-id="{{id}}">
        {{#space}}
        <header class="clearfix">
            <h3 class="alignleft"><a href="{{space.singleUrl}}">{{space.name}}</a></h3>
            <a class="toggle-mapa" href="#"><span class="ver-mapa">ver mapa</span><span class="ocultar-mapa">ocultar mapa</span> <span class="icone icon_pin"></span></a>
        </header>
        {{/space}}
        <div class="infos">
            <!--p class="label">Resumo da regra que será exibido pro público.</p-->
            <p><span class="label">Horário inicial:</span> {{rule.startsAt}}</p>
            <p><span class="label">Horário final:</span> {{rule.endsAt}}</p>
            <p><span class="label">Data inicial:</span> {{rule.screen_startsOn}}</p>
            {{#rule.screen_until}}<p><span class="label">Data final:</span> {{rule.screen_until}}</p><!--(Se repetir mostra o campo de data final)-->{{/rule.screen_until}}
        </div>
        <!-- .infos -->
        {{#space}}
        <div id="occurrence-map-{{id}}" class="mapa js-map" data-lat="{{space.location.latitude}}" data-lng="{{space.location.longitude}}"></div>
        <!-- .mapa -->
        {{/space}}
    </div>
<?php $eventOccurrenceItemTemplate_VIEW = ob_get_clean(); ?>

<?php $this->part('editable-entity', array('entity' => $entity, 'action' => $action));  ?>
<div class="barra-esquerda barra-lateral evento">
	<div class="setinha"></div>
    <div class="bloco">
		<a class="oficial" href="#">Evento da Prefeitura</a>
    </div>
    <?php $this->part('redes-sociais', array('entity'=>$entity)); ?>
        <?php if(is_editable()): ?>
        <div class="bloco">
            <h3 class="subtitulo">Projeto</h3>
            <a class="js-search js-include-editable"
                data-field-name='projectId'
                data-emptytext="Selecione um projeto"
                data-search-box-width="400px"
                data-search-box-placeholder="Selecione um projeto"
                data-entity-controller="project"
                data-search-result-template="#agent-search-result-template"
                data-selection-template="#agent-response-template"
                data-no-result-template="#agent-response-no-results-template"
                data-selection-format="chooseProject"
                data-allow-clear="1"
                data-auto-open="true"
                data-value="<?php echo $entity->project ? $entity->project->id : ''; ?>"
                data-value-name="<?php echo $entity->project ? $entity->project->name : ''; ?>"
                title="Selecionar um Projeto">
                <?php echo $entity->project ? $entity->project->name : ''; ?>
            </a>
        </div>
        <?php elseif($entity->project): ?>
        <div class="bloco">
            <h3 class="subtitulo">Projeto</h3>
            <span><a href="<?php echo $entity->project->singleUrl; ?>"><?php echo $entity->project->name; ?></a></span>
        </div>
        <?php endif; ?>
</div>
<article class="col-60 main-content evento">
    <header class="main-content-header">
        <div
        <?php if ($header = $entity->getFile('header')): ?>
                style="background-image: url(<?php echo $header->transform('header')->url; ?>);" class="imagem-do-header com-imagem js-imagem-do-header"
            <?php else: ?>
                class="imagem-do-header js-imagem-do-header"
            <?php endif; ?>
            >
                <?php if (is_editable()): ?>
                <a class="botao editar js-open-dialog" data-dialog="#dialog-change-header" href="#">editar</a>
                <div id="dialog-change-header" class="js-dialog" title="Editar Imagem da Capa">
                    <?php add_ajax_uploader($entity, 'header', 'background-image', '.js-imagem-do-header', '', 'header'); ?>
                </div>
            <?php endif; ?>
        </div>
        <!--.imagem-do-header-->
        <div class="content-do-header">
            <?php if ($avatar = $entity->avatar): ?>
                <div class="avatar com-imagem">
                    <img src="<?php echo $avatar->transform('avatarBig')->url; ?>" alt="" class="js-avatar-img" />
                <?php else: ?>
                    <div class="avatar">
                        <img class="js-avatar-img" src="<?php echo $app->assetUrl ?>/img/avatar-padrao.png" />
                    <?php endif; ?>
                    <?php if (is_editable()): ?>
                        <a class="botao editar js-open-dialog" data-dialog="#dialog-change-avatar" href="#">editar</a>
                        <div id="dialog-change-avatar" class="js-dialog" title="Editar avatar">
                            <?php add_ajax_uploader($entity, 'avatar', 'image-src', 'div.avatar img.js-avatar-img', '', 'avatarBig'); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!--.avatar-->


                <h2><span class="js-editable" data-edit="name" data-original-title="Nome de exibição" data-emptytext="Nome de exibição"><?php echo $entity->name; ?></span></h2>
                <div class="objeto-meta">
                    <div>
                        <span class="label">Linguagens: </span>
                        <?php if (is_editable()): ?>
                            <span id="term-linguagem" class="js-editable-taxonomy" data-original-title="Linguagens" data-emptytext="Selecione pelo menos uma linguagem" data-restrict="true" data-taxonomy="linguagem"><?php echo implode(', ', $entity->terms['linguagem']) ?></span>
                        <?php else: ?>
                            <?php foreach ($entity->terms['linguagem'] as $i => $term): if ($i)
                                    echo ', ';
                                ?><a href="<?php echo $app->createUrl('site', 'search') ?>#taxonomies[linguagem][]=<?php echo $term ?>"><?php echo $term ?></a><?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (is_editable() || !empty($entity->terms['tag'])): ?>
                            <span class="label">Tags: </span>
                            <?php if (is_editable()): ?>
                                <span class="js-editable-taxonomy" data-original-title="Tags" data-emptytext="Insira tags" data-taxonomy="tag"><?php echo implode(', ', $entity->terms['tag']) ?></span>
                            <?php else: ?>
                                <?php foreach ($entity->terms['tag'] as $i => $term): if ($i)
                                        echo ', ';
                                    ?><a href="<?php echo $app->createUrl('site', 'search') ?>#taxonomies[tags][]=<?php echo $term ?>"><?php echo $term ?></a><?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <!--.objeto-meta-->
            </div>
    </header>
    <!--.main-content-header-->
    <!--aqui entram as abas quando tiver contas e repercussão funcionando-->
    <div id="sobre" class="aba-content">
        <div class="ficha-spcultura">
            <p>
                <span class="js-editable" data-edit="shortDescription" data-original-title="Descrição Curta" data-emptytext="Insira uma descrição curta do evento"><?php echo $entity->shortDescription; ?></span>
            </p>
            <div class="servico">
                <p><span class="label">Inscrições:</span><span class="js-editable" data-edit="registrationInfo" data-original-title="Inscrições" data-emptytext="Informações sobre as inscrições"><?php echo $entity->registrationInfo; ?></span></p>

                <?php if (is_editable() || $entity->classificacaoEtaria): ?>
                    <p><span class="label">Classificação Etária: </span><span class="js-editable" data-edit="classificacaoEtaria" data-original-title="Classificação Etária" data-emptytext="Informe a classificação etária do evento"><?php echo $entity->classificacaoEtaria; ?></span></p>
                <?php endif; ?>

                <?php if (is_editable() || $entity->preco): ?>
                    <p><span class="label">Entrada: </span><span class="js-editable" data-edit="preco" data-original-title="Preço" data-emptytext="Informe o preço do evento"><?php echo $entity->preco; ?></span></p>
                <?php endif; ?>

                <?php if (is_editable() || $entity->site): ?>
                    <p><span class="label">Site:</span>
                        <?php if (is_editable()): ?>
                            <span class="js-editable" data-edit="site" data-original-title="Site" data-emptytext="Informe o endereço do site do evento"><?php echo $entity->site; ?></span></p>
                    <?php else: ?>
                        <a class="url" href="<?php echo $entity->site; ?>"><?php echo $entity->site; ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <p><span class="label">Mais Informações:</span> (000) 0000-0000</p>
                <p><span class="label">Duração:</span> 000min</p>
                <p><span class="label">Acessibilidade:</span> tradução em libras/descrição sonora</p>
            </div>
            <!--.servico-->
            <div class="servico ocorrencia clearfix">
                <?php
                //$entity->getMetaLists(array('group'=>'links'));
                $occurrences = $entity->occurrences ? $entity->occurrences->toArray() : array();
                ?>
                <?php if (is_editable() || $occurrences): ?>
                    <div class="js-event-occurrence">
                        <?php

                        $screenFrequencies = getOccurrenceFrequencies();
                        $mustache = new Mustache_Engine();

                        if ($occurrences) {

                            $spaces = array();

                            usort($occurrences, function($a, $b) {
                                return $a->space->id - $b->space->id;
                            });

                            foreach ($occurrences as $occurrence) {

                                $templateData = json_decode(json_encode($occurrence));
                                $templateData->rule->screen_startsOn = $occurrence->rule->startsOn ? (new DateTime($occurrence->rule->startsOn))->format('d/m/Y') : '';
                                $templateData->rule->screen_until = $occurrence->rule->until ? (new DateTime($occurrence->rule->until))->format('d/m/Y') : '';
                                $templateData->rule->screen_frequency = $occurrence->rule->frequency ? $screenFrequencies[$templateData->rule->frequency] : '';
                                $templateData->serialized = json_encode($templateData);
                                $templateData->formAction = $occurrence->editUrl;

                                if (is_editable()) {
                                    echo $mustache->render($eventOccurrenceItemTemplate, $templateData);
                                } else {
                                    if (!array_key_exists($occurrence->space->id, $spaces))
                                        $spaces[$occurrence->space->id] = true;
                                    else
                                        $templateData->space = null;

                                    echo $mustache->render($eventOccurrenceItemTemplate_VIEW, $templateData);
                                }

                            }

                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <!--.servico.ocorrencia-->
            <?php if(is_editable()): ?>
                <div class="textright">
                    <a class="botao adicionar js-open-dialog hltip" data-dialog="#dialog-event-occurrence" href="#"
                       data-dialog-callback="MapasCulturais.eventOccurrenceUpdateDialog"
                       data-dialog-title="Adicionar Ocorrência"
                       data-form-action='insert'
                       title="Clique para adicionar ocorrências">
                        Adicionar Ocorrência
                    </a>
                </div>
            <?php endif; ?>
            <div id="dialog-event-occurrence" class="js-dialog" title="Adicionar Ocorrência">
                <?php if($this->controller->action == 'create'): ?>
                    <span class="js-dialog-disabled" data-message="Primeiro Salve"></span>
                <?php else: ?>
                    <div class="js-dialog-content"></div>
                <?php endif; ?>
            </div>
        </div>
        <!--.ficha-spcultura-->

        <?php if (is_editable() || $entity->longDescription): ?>
            <h3>Descrição</h3>
            <div class="descricao js-editable" data-edit="longDescription" data-original-title="Descrição" data-emptytext="Insira uma descrição detalhada do evento" data-placeholder="Insira uma descrição do espaço" data-showButtons="bottom" data-placement="bottom"><?php echo $entity->longDescription; ?></div>
        <?php endif; ?>


        <!-- Video Gallery BEGIN -->
        <?php $app->view->part('parts/video-gallery.php', array('entity' => $entity)); ?>
        <!-- Video Gallery END -->

        <!-- Image Gallery BEGIN -->
        <?php $app->view->part('parts/gallery.php', array('entity' => $entity)); ?>
        <!-- Image Gallery END -->
    </div>
    <!-- #sobre.aba-content -->
    <?php $this->part('parts/owner', array('entity' => $entity, 'owner' => $entity->owner)) ?>
</article>
<!--.main-content-->
<div class="barra-lateral evento barra-direita">
    <div class="setinha"></div>
    <!-- Related Agents BEGIN -->
    <?php $app->view->part('parts/related-agents.php', array('entity' => $entity)); ?>
    <!-- Related Agents END -->


    <!-- Downloads BEGIN -->
    <?php $app->view->part('parts/downloads.php', array('entity' => $entity)); ?>
    <!-- Downloads END -->

    <!-- Link List BEGIN -->
    <?php $app->view->part('parts/link-list.php', array('entity' => $entity)); ?>
    <!-- Link List END -->
</div>
<?php if (is_editable()): ?>
<script id="event-occurrence-form" type="text/html" class="js-mustache-template">
    <form action="{{formAction}}" method="POST">
        <div class="mensagem erro escondido"></div>
        <input type="hidden" name="eventId" value="<?php echo $entity->id; ?>"/>
        <input id="espaco-do-evento" type="hidden" name="spaceId" value="{{space.id}}">

        <div class="clearfix js-space">
            <label>Espaço:</label><br>
            <span class="js-search-occurrence-space"
                data-field-name='spaceId'
                data-emptytext="Selecione um espaço"
                data-search-box-width="400px"
                data-search-box-placeholder="Selecione um espaço"
                data-entity-controller="space"
                data-search-result-template="#agent-search-result-template"
                data-selection-template="#agent-response-template"
                data-no-result-template="#agent-response-no-results-template"
                data-selection-format="chooseSpace"
                data-auto-open="true"
                data-value="{{space.id}}"
                title="Selecione um espaço"
                >{{space.name}}</span>
        </div>

        <!--mostrar se não encontrar o espaço cadastrado
        <div class="mensagem alerta">
            Aparentemente o espaço procurado ainda não se encontra registrado em nosso sistema. Tente uma nova busca ou antes de continuar, adicione um novo espaço clicando no botão abaixo.
        </div>
        <a class="botao adicionar" href="#">adicionar espaço</a>-->
        <div class="clearfix">
            <div class="grupo-de-campos">
                <label for="horario-de-inicio">Horário inicial:</label><br>
                <input id="horario-de-inicio" class="horario-da-ocorrencia js-event-times" type="text" name="startsAt" placeholder="00:00" value="{{rule.startsAt}}">
            </div>
            <div class="grupo-de-campos">
                <label for="horario-de-fim">Horário final:</label><br>
                <input id="horario-de-fim" class="horario-da-ocorrencia js-event-times" type="text" name="endsAt" placeholder="00:00"  value="{{rule.endsAt}}">
            </div>
            <div class="grupo-de-campos">
                <span class="label">Frequência:</span><br>
                    <select name="frequency" class="js-select-frequency">
                        <option value="once" {{#rule.freq_once}}selected="selected"{{/rule.freq_once}}>uma vez</option>
                        <option value="daily" {{#rule.freq_daily}}selected="selected"{{/rule.freq_daily}}>todos os dias</option>
                        <option value="weekly" {{#rule.freq_weekly}}selected="selected"{{/rule.freq_weekly}}>semanal</option>
                        <!-- for now we will not support monthly recurrences.
                        <option value="monthly" {{#rule.freq_monthly}}selected="selected"{{/rule.freq_monthly}}>mensal</option>
                        -->
                    </select>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="grupo-de-campos">
                <label for="data-de-inicio">Data inicial:</label><br>
                <input id="starts-on-{{id}}-visible" type="text" class="js-event-dates data-da-ocorrencia" readonly="readonly" placeholder="00/00/0000" value="{{rule.screen_startsOn}}">
                <input id="starts-on-{{id}}" name="startsOn" type="hidden" data-alt-field="#starts-on-{{id}}-visible" value="{{rule.startsOn}}"/>
            </div>
            <div class="grupo-de-campos">
                <label for="data-de-fim">Data final:</label><br>
                <input id="until-{{id}}-visible" type="text" class="js-event-dates data-da-ocorrencia" readonly="readonly" placeholder="00/00/0000" value="{{rule.screen_until}}">
                <input id="until-{{id}}" name="until" type="hidden" value="{{rule.until}}"/>
                <!--(Se repetir mostra o campo de data final)-->
            </div>
            <div class="alignleft js-freq-hide js-weekly">
                <span class="label">Repete:</span><br>
                <div>
                    <label><input type="checkbox" name="day[0]" {{#rule.day.0}}checked="checked"{{/rule.day.0}}/> D </label>
                    <label><input type="checkbox" name="day[1]" {{#rule.day.1}}checked="checked"{{/rule.day.1}}/> S </label>
                    <label><input type="checkbox" name="day[2]" {{#rule.day.2}}checked="checked"{{/rule.day.2}}/> T </label>
                    <label><input type="checkbox" name="day[3]" {{#rule.day.3}}checked="checked"{{/rule.day.3}}/> Q </label>
                    <label><input type="checkbox" name="day[4]" {{#rule.day.4}}checked="checked"{{/rule.day.4}}/> Q </label>
                    <label><input type="checkbox" name="day[5]" {{#rule.day.5}}checked="checked"{{/rule.day.5}}/> S </label>
                    <label><input type="checkbox" name="day[6]" {{#rule.day.6}}checked="checked"{{/rule.day.6}}/> S </label>
                </div>
                <!-- for now we will not support monthly recurrences.
                <div>
                    <label style="display:inline;"><input type="radio" name="monthly" value="month" {{#rule.monthly_month}}checked="checked"{{/rule.monthly_month}}/> dia do mês </label>
                    <label style="display:inline;"><input type="radio" name="monthly" value="week" {{#rule.monthly_week}}checked="checked"{{/rule.monthly_week}}/> dia da semana </label>
                </div>
                -->
            </div>
        </div>
        <p class="staging-hidden">
            <span class="label">Resumo:</span><br>
            Resumo da regra
        </p>
        <footer class="clearfix">
            <input type="submit" value="enviar">
        </footer>
    </form>
</script>
<?php endif; ?>
<script type="text/html" id="event-occurrence-item" class="js-mustache-template">
    <?php echo $eventOccurrenceItemTemplate; ?>
</script>