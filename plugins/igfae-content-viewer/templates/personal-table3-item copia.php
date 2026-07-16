<?php 
    $publications = $item->getPublications();
    $projects = $item->getProjects();
    $researchProg = $item->getResearchProg();
    $numProjects=5;
    $numArticles=5;
?>
<div class="col-xs-12 table-row">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 personal-surname table-col">
            <button class="btn-collapse collapsed btn-link" role="button" data-toggle="collapse" data-target="#personal-<?php echo $key; ?>"  aria-expanded="false" aria-controls="personal-<?php echo $key; ?>" title="<?php _e( 'Collapse', 'ifgae-content-viewer'); ?>"><i class="ti ti-arrow-circle-down"></i><i class="ti ti-arrow-circle-up"></i></button>
            <strong><?php echo $item->getSurname(); ?>, <?php echo $item->getName(); ?></strong>
            <span>id: <?php echo $item->getId(); ?></span>
        </div>

        <div class="hidden-xs hidden-sm col-md-3 personal-phone table-col">
            <?php if ( $item->getPhone() ) : ?>
                (+34) 8818<?php echo $item->getPhone(); ?>
            <?php else: ?>
                -
            <?php endif; ?>
        </div>

        <div class="hidden-xs hidden-sm col-md-3 personal-email table-col">
            <a href="mailto:<?php echo $item->getEmail(); ?>"><?php echo $item->getEmail(); ?></a> 
        </div>

        <div class="hidden-xs hidden-sm col-md-3 personal-category table-col">
            <?php echo $item->getCategory(); ?>
        </div>

        <div class="col-xs-12">
            <div id="personal-<?php echo $key; ?>" class="collapse" data-parent="#accordion-parent">
                
                <div class="row">
                    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <?php if( $item->getImgUrl() ): ?> 
                            <a href="#" data-toggle="modal" data-target="#personal-img-<?php echo $key; ?>" class="text-center">
                                <div class="img-container text-center">

                                    <img alt="<?php echo $item->getFullName(); ?>" class="img img-card" src="<?php echo $item->getImgUrl(); ?>"/>

                                    <div id="personal-img-<?php echo $key; ?>" class="modal" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <img alt="<?php echo $item->getFullName(); ?>" class="img img-card img-responsive full-width" src="<?php echo $item->getImgUrl(); ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="img-container img-notfound">
                                <i class="ti ti-user" ></i>
                            </div>
                        <?php endif; ?>


                        <div class="social-links">
                            <ul class="list-unstyled list-inline">
                                <?php if( $item->getTwitter() ): ?>
                                    <li><a title="Twitter" href="https://twitter.com/<?php echo $item->getTwitter(); ?>"><i class="ti ti-twitter"></i></a></li>
                                <?php endif; ?>

                                <?php if( $item->getLinkedin() ): ?>
                                    <li><a title="LinkedIn" href="https://www.linkedin.com/in/<?php echo $item->getLinkedin(); ?>"><i class="ti ti-linkedin"></i></a></li>
                                <?php endif; ?>

                                <?php if( $item->getWebpage() ): ?>
                                    <li><a href="<?php echo $item->getWebpage(); ?>" title="<?php echo $item->getWebpage(); ?>"><i class="ti ti-world"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="links-container">
                            <dl>
                                <?php if( $item->getOrcidID() ): ?>
                                    <dt>Orcid ID:</dt>
                                    <dd><a title="Orcid ID" href="https://orcid.org/<?php echo $item->getOrcidID(); ?>"> <?php echo $item->getOrcidID(); ?> </a></dd>
                                <?php endif; ?>

                                <?php if( $item->getScopusID() ): ?>
                                    <dt>Scopus Author ID:</dt>
                                    <dd><a title="Scopus Author ID" href="http://www.scopus.com/authid/detail.url?authorId=<?php echo $item->getScopusID(); ?>" ><?php echo $item->getScopusID(); ?></a></dd>
                                <?php endif; ?>

                                <?php if( $item->getResearchID() ): ?>
                                    <dt>Research ID:</dt>
                                    <dd><a title="Research ID" href="http://reserchid.com/rid/<?php echo $item->getResearchID(); ?>" ><?php echo $item->getResearchID(); ?></a></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-9 col-md-10 col-lg-10">
                        <ul class="list-unstyled hidden-md hidden-lg contact-info">
                            <?php if( $item->getPhone() ): ?>
                                <li>
                                    <strong><?php _e('Phone', 'ifgae-content-viewer' ); ?>: </strong>
                                    (+34) 8818<?php echo $item->getPhone(); ?>
                                </li>
                            <?php endif; ?>

                            <?php if( $item->getEmail() ): ?>
                                <li>
                                    <strong><?php _e('Email', 'ifgae-content-viewer' ); ?>: </strong>
                                    <a href="mailto:<?php echo $item->getEmail(); ?>"><?php echo $item->getEmail(); ?></a> 
                                </li>
                            <?php endif; ?>

                            <?php if( $item->getCategory() ): ?>
                                <li>
                                    <strong><?php _e( 'Category', 'ifgae-content-viewer' ); ?>: </strong>
                                    <?php echo $item->getCategory(); ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <?php if ( trim($item->getDescription()) ): ?>
                            <div class="description-container">
                                <strong><?php _e( 'Biography', 'ifgae-content-viewer' ); ?>: </strong>
                                <p><?php echo $item->getDescription(); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ( $item->getArea() ): ?>
                            <div class="group-container">
                                <strong><?php _e( 'Research Area', 'ifgae-content-viewer' ); ?>: </strong>
                                
                                <?php if ( $item->getAreaURL() ): ?>
                                    <a href="<?php echo $item->getAreaURL(); ?>">
                                        <span class="personal-group"><?php echo $item->getArea(); ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="personal-group"><?php echo $item->getArea(); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $researchProg ): ?>
                            <div class="group-container">
                                <strong><?php _e( 'Research Program', 'ifgae-content-viewer' ); ?>: </strong>
                                <span class="personal-group"> <?php echo implode(', ',array_map(function($elem) {return $elem['name'];},$researchProg)); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <?php  if ( $publications  ): ?>
                                <div class="col-xs-12 col-sm-6 col-md-6 publications-container">

                                    <h4><?php _e( 'Recent Publications', 'ifgae-content-viewer' ); ?></h4>

                                    <ul class="list-publications">
                                    <?php foreach( array_slice($publications,-$numArticles,$numArticles) as $publication ): ?>
                                        <li><?php list($publicationTitle,$publicationLink)=explode('#',$publication); echo $publicationTitle; ?> 
                                            <a class="ti-link" href="<?php echo 'https://doi.org/'.$publicationLink;?>"></a></li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>

                            <?php endif; ?>

                            <?php if ( $projects ): ?>
                                <div class="col-xs-12 col-sm-6 col-md-6 projects-container">
                                    <h4><?php _e( 'Recent Projects', 'ifgae-content-viewer' ); ?></h4>
                                    <ul class="list-projects">
                                    <?php foreach( array_slice($projects,-$numProjects,$numProjects) as $project ): ?>
                                        <li><?php echo $project; ?></li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
