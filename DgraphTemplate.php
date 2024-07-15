<?php
/**
 * Dgraph - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 */

/**
 * QuickTemplate class for Dgraph skin
 * @ingroup Skins
 */
use MediaWiki\ResourceLoader\SkinModule;
use MediaWiki\MediaWikiServices;

class DgraphTemplate extends BaseTemplate {
	/* Functions */

	public $assetsPath = '';
	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		$assetsPath = htmlspecialchars( $this->config->get( 'LocalStylePath' ) ) . '/DGraph';

		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];

		$user = $this->getSkin()->getUser();
		$instance = MediaWiki\MediaWikiServices::getInstance();
		$isWatched = $instance->getWatchedItemStore()->isWatched(
			$user,
			$this->getSkin()->getRelevantTitle()
		);
		$mode = $isWatched
			? 'unwatch'
			: 'watch';

		if ( isset( $nav['actions'][$mode] ) ) {
			$nav['views'][$mode] = $nav['actions'][$mode];
			$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
			$nav['views'][$mode]['primary'] = true;
			unset( $nav['actions'][$mode] );
		}
		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeIdForAttribute( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}

		// Output HTML Page
		$logos = SkinModule::getAvailableLogos( $this->getSkin()->getConfig() );
		$wordmark = $logos['wordmark'] ?? [
			"src" => "$assetsPath/assets/images/logo.svg",
			"width" => 233,
			"height" => 70,
		];

		?>
		  <header id="page-header" class="page-header">
			<div class="page-header-primary">
			  <div class="container">
				<div class="row">
				  <div class="col-6 col-desktop-4">
					<figure class="page-logo"><a href="<?php
					echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] )
					?>"><img src="<?php echo $wordmark["src"] . '" width="' . $wordmark["width"]
						.'" height="' . $wordmark["height"] . '"' ?>alt=""></a></figure>
				  </div>
				  <div class="col-6 col-desktop-8">
					<nav class="page-nav">
					  <ul class="page-nav__list page-nav__list--right">
						<li class="page-nav__item">
							<?php $this->navLink( 'github', [
								"style" => "mega",
								"class" => "github-button"
							])?>
						</li>
					  </ul>
					</nav>
				  </div>
				</div>
			  </div>
			</div>
			<!-- / page-header-primary-->
			<div class="page-header-secondary">
			  <div class="container">
				<div class="row">
				  <div class="col-12">
					<nav class="page-nav">
					  <ul class="page-nav__list page-nav__list--box dropdown-environment">
						<li class="page-nav__item page-nav__item--box">
						  <form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="form page-nav__form">
							<input type="hidden" value="Special:Search" name="title">
							<input type="search" name="search" placeholder="" title="Search dgraph [alt-shift-f]" accesskey="f" id="searchInput" tabindex="1" autocomplete="off" class="form__input page-nav__form-input">
							<input id="submit-search" name="submit" type="image" src="<?php echo $assetsPath; ?>/assets/images/icon-form-search.png" alt="Submit" class="page-nav__form-submit">
						  </form>
						</li>
						<li class="page-nav__item page-nav__item--box"><a href="#settings-dropdown" class="page-nav__link js-dropdown-trigger dropdown-trigger"><img src="<?php echo $assetsPath; ?>/assets/images/icon-gear.png" alt="Settings" class="page-nav__link-icon"></a>
						  <div id="settings-dropdown" class="dropdown page-nav__settings-dropdown"> 
							<ul class="dropdown__list"<?php $this->html( 'userlangattributes' ) ?>>
								<?php
								foreach ( $this->data['view_urls'] as $link ) {
									?>
									<li class="dropdown__item"<?php echo $link['attributes'] ?>><span><a class="dropdown__link" href="<?php
											echo htmlspecialchars( $link['href'] )
											?>" <?php
											echo $link['key'];
											if ( isset ( $link['rel'] ) ) {
												echo ' rel="' . htmlspecialchars( $link['rel'] ) . '"';
											}
											?>><?php
												// $link['text'] can be undefined - bug 27764
												if ( array_key_exists( 'text', $link ) ) {
													echo array_key_exists( 'img', $link )
														? '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />'
														: htmlspecialchars( $link['text'] );
												}
												?></a></span></li>
								<?php
								}
								foreach ( $this->data['action_urls'] as $link ) {
									?>
									<li class="dropdown__item"<?php echo $link['attributes'] ?>>
										<a class="dropdown__link" href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" <?php
										echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] )
											?></a>
									</li>
								<?php
								}
								?>
							</ul>
						  </div>
						</li>
						<li class="page-nav__item page-nav__item--box"><a href="#user-dropdown" class="page-nav__link js-dropdown-trigger dropdown-trigger"><img src="<?php echo $assetsPath; ?>/assets/images/icon-user.png" alt="User Settings" class="page-nav__link-icon"></a>
						  <div id="user-dropdown" class="dropdown page-nav__user-dropdown"> 
							<ul class="dropdown__list"<?php $this->html( 'userlangattributes' ) ?>>
								<?php
								$personalTools = $this->getPersonalTools();
								foreach ( $personalTools as $key => $item ) {
									$item['class'] = 'dropdown__item';
									if ( isset( $item['links'] ) ) {
										foreach ( $item['links'] as $linkKey => &$link ) {
											$link['class'] = 'dropdown__link';
										}
									} else { 
										// TODO
									}
									echo $this->makeListItem( $key, $item );
								}
								?>
							</ul>
						  </div>
						</li>
					  </ul>
					  <ul class="page-nav__list">
					<?php
					$nav = $this->data['sidebar']['navigation'] ?? [];
					foreach( array_slice( $nav, 0, 2 ) as $item ) {
					?>
						<li class="page-nav__item">
							<a href="<?php echo $item['href']?>"
								class="page-nav__link">
								<?php echo $item['text']; ?>
							</a></li>
					<?php
					}
					?>
					</ul>
					</nav>
				  </div>
				</div>
			  </div>
			</div>
			<!-- / page-header-secondary
			<div class="page-header-tertiary">
			  <div class="container">
				<div class="row">
				  <div class="col-12">
					<?php
					if ( $this->data['sitenotice'] ) {
						?>
						<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
					<?php
					}
					if ( is_callable( array( $this, 'getIndicators' ) ) ) {
						echo $this->getIndicators();
					}
					?>
					<nav class="page-nav">
					  <ul class="page-nav__list">
						<li class="page-nav__item"><a href="#" class="page-nav__link is-active">Main Page</a>
						  <div class="arrow-box"></div>
						</li>
						<li class="page-nav__item"><a href="#" class="page-nav__link">Discussion</a></li>
						<li class="page-nav__item page-nav__item--right dropdown-environment"><a href="#more-dropdown" class="page-nav__button js-dropdown-trigger dropdown-trigger">
							 
							More<img src="<?php echo $assetsPath; ?>/assets/images/icon-chevron-down.png" alt="More information" class="page-nav__link-icon"></a>
						  <div id="more-dropdown" class="dropdown page-nav__more-dropdown"> 
						  </div>
						</li>
					  </ul>
					</nav>
				  </div>
				</div>
			  </div>
			</div>
			  -->
		  </header>
  <!-- / page-header-->
  <div class="page-content page-content--page">
    <div class="container">
      <div class="row">
        <div class="col-12">
		  <header class="page-content__header">
			<?php
			// Loose comparison with '!=' is intentional, to catch null and false too, but not '0'
			if ( $this->data['title'] != '' ) {
			?>
			<h1 id="firstHeading" class="firstHeading"><?php
				 $this->html( 'title' )
			?></h1>
			<?php
			} ?>
		  </header>
			<?php $this->html( 'prebodyhtml' ) ?>
			<div id="bodyContent" class="mw-body-content">
				<?php
				if ( $this->data['isarticle'] ) {
					?>
					<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
				<?php
				}
				?>
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php
					$this->html( 'subtitle' )
				?></div>
				<?php
				if ( $this->data['undelete'] ) {
					?>
					<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
				<?php
				}
				?>
				<?php
				if ( $this->data['newtalk'] ) {
					?>
					<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
				<?php
				}
				?>
				<div id="jump-to-nav" class="mw-jump">
					<?php $this->msg( 'jumpto' ) ?>
					<a href="#mw-head"><?php
						$this->msg( 'jumptonavigation' )
					?></a><?php $this->msg( 'comma-separator' ) ?>
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<?php
				$this->html( 'bodycontent' );

				if ( $this->data['printfooter'] ) {
					?>
					<div class="printfooter">
						<?php $this->html( 'printfooter' ); ?>
					</div>
				<?php
				}

				if ( $this->data['catlinks'] ) {
					$this->html( 'catlinks' );
				}

				if ( $this->data['dataAfterContent'] ) {
					$this->html( 'dataAfterContent' );
				}
				?>
				<div class="visualClear"></div>
				<?php $this->html( 'debughtml' ); ?>
			</div>
        </div>
      </div>
    </div>
  </div>
  <div class="page-footer__wrapper">
    <footer id="page-footer" class="page-footer">
      <div class="container">
        <div class="row">
          <div class="col-12 col-tablet-3">
            <figure class="page-logo-footer"><a href="<?php
					echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] );
					// scale down from 233x70 to
					$logoWidth = ceil( $wordmark['width'] * 0.6 );
					$logoHeight = ceil( $wordmark['height'] * 0.6 );
					?>">
					<img src="<?php echo $wordmark['src']?>"
						width="<?php echo $wordmark['width']?>"
						height="<?php echo $wordmark['height']?>" alt="<?php echo $this->msg('sitename') ?>">
				</a></figure>
          </div>
          <div class="col-4 col-tablet-2">
            <nav class="page-footer-nav">
              <h6 class="page-footer-nav__title"><?php echo $this->getMsg( 'dgraph-header-company' ) ?></h6>
              <ul class="page-footer-nav__list">
              <?php
                $validFooterLinks = $this->getFooterLinks();
                foreach( ( $validFooterLinks['places'] ?? [] ) as $key => $link ) {
                    ?><li class="page-footer-nav__item"><?php
                    echo Html::rawElement( 'span', [
                        'id' => 'footer-' . $link,
                    ], $this->get( $link ) );
                    ?></li><?php
                }?>
              </ul>
            </nav>
          </div>
          <div class="col-4 col-tablet-2">
            <nav class="page-footer-nav">
              <h6 class="page-footer-nav__title"><?php echo $this->getMsg( 'dgraph-header-community' ) ?></h6>
              <ul class="page-footer-nav__list">
                <?php
                echo $this->footerNavItem( 'community-1' );
                echo $this->footerNavItem( 'community-2' );
                echo $this->footerNavItem( 'github' );
                echo $this->footerNavItem( 'community-3' );
                echo $this->footerNavItem( 'community-4' );
                ?>
              </ul>
            </nav>
          </div>
          <div class="col-4 col-tablet-2">
            <nav class="page-footer-nav">
              <h6 class="page-footer-nav__title"><?php echo $this->getMsg( 'dgraph-header-connect' ) ?></h6>
              <ul class="page-footer-nav__list">
                <?php
                echo $this->footerNavItem( 'twitter' );
                echo $this->footerNavItem( 'connect-1' );
                ?>
              </ul>
            </nav>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
			<?php
			$validFooterLinks = $this->getFooterLinks();
			foreach( ( $validFooterLinks['info'] ?? [] ) as $key => $link ) {
				if ( $link === 'copyright' ) {
					echo Html::rawElement( 'span', [
						'id' => 'footer-' . $link,
						'class' => 'imprint__copyrights',
					], $this->get( $link ) );
				}
			}?>
		</div>

        </div>
      </div>
    </footer>
    <!-- / page-footer-->
  </div>
  <!-- / page-footer__wrapper-->
  <!-- GitHub star button-->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
<?php
	}

	private function navLink( $id, $attrs = [] ) {
		$key = 'dgraph-link-' . $id . '-';
		$url = $this->getMsg( $key . 'url' )->escaped();
		$title = $this->getMsg( $key . 'title' )->escaped();
		$label = $this->getMsg( $key . 'text' )->text();
		$className = $attrs["class"] ?? '';
		echo Html::element( 'a', $attrs + [
			'title' => $title,
			'href' => $url,
			"class" => "page-footer-nav__link link-alias " . $className,
		], $label );
	}

	private function footerNavItem( $id ) {
		$key = 'dgraph-link-' . $id . '-';
		$url = $this->getMsg( $key . 'url' );
		if ( $url->isDisabled() ) {
			return;
		}
		?>
		<li class="page-footer-nav__item">
			<?php
			$this->navLink( $id );
			?>
		</li>
		<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param array $portals
	 */
	protected function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			switch ( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] !== false ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
					break;
			}
		}
	}

	/**
	 * @param string $name
	 * @param array $content
	 * @param null|string $msg
	 * @param null|string|array $hook
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = wfMessage( $msg );
		$labelId = Sanitizer::escapeIdForAttribute( "p-$name-label" );
		?>
		<div class="portal" role="navigation" id='<?php
		echo Sanitizer::escapeIdForAttribute( "p-$name" )
		?>'<?php
		echo Linker::tooltip( 'p-' . $name )
		?> aria-labelledby='<?php echo $labelId ?>'>
			<h3<?php $this->html( 'userlangattributes' ) ?> id='<?php echo $labelId ?>'><?php
				echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg );
				?></h3>

			<div class="body">
				<?php
				if ( is_array( $content ) ) {
					?>
					<ul>
						<?php
						foreach ( $content as $key => $val ) {
							echo $this->makeListItem( $key, $val );
						}
						if ( $hook !== null ) {
							MediaWikiServices::getInstance()->getHookContainer()->run( $hook, array( &$this, true ) );
						}
						?>
					</ul>
				<?php
				} else {
					echo $content; /* Allow raw HTML block to be defined by extensions */
				}

				$this->renderAfterPortlet( $name );
				?>
			</div>
		</div>
	<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 *
	 * @param array $elements
	 */
	protected function renderNavigation( $elements ) {
		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
			// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $this->data['rtl'] ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			switch ( $element ) {
				case 'NAMESPACES':
					?>
					<div id="p-namespaces" role="navigation" class="vectorTabs<?php
					if ( count( $this->data['namespace_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-namespaces-label">
						<h3 id="p-namespaces-label"><?php $this->msg( 'namespaces' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['namespace_urls'] as $link ) {
								?>
								<li <?php echo $link['attributes'] ?>><span><a href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" <?php
										echo $link['key'];
										if ( isset ( $link['rel'] ) ) {
											echo ' rel="' . htmlspecialchars( $link['rel'] ) . '"';
										}
										?>><?php
											echo htmlspecialchars( $link['text'] )
											?></a></span></li>
							<?php
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'VARIANTS':
					?>
					<div id="p-variants" role="navigation" class="vectorMenu<?php
					if ( count( $this->data['variant_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-variants-label">
						<?php
						// Replace the label with the name of currently chosen variant, if any
						$variantLabel = $this->getMsg( 'variants' )->text();
						foreach ( $this->data['variant_urls'] as $link ) {
							if ( stripos( $link['attributes'], 'selected' ) !== false ) {
								$variantLabel = $link['text'];
								break;
							}
						}
						?>
						<h3 id="p-variants-label">
							<span><?php echo htmlspecialchars( $variantLabel ) ?></span><a href="#"></a>
						</h3>

						<div class="menu">
							<ul>
								<?php
								foreach ( $this->data['variant_urls'] as $link ) {
									?>
									<li<?php echo $link['attributes'] ?>><a href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" lang="<?php
										echo htmlspecialchars( $link['lang'] )
										?>" hreflang="<?php
										echo htmlspecialchars( $link['hreflang'] )
										?>" <?php
										echo $link['key']
										?>><?php
											echo htmlspecialchars( $link['text'] )
											?></a></li>
								<?php
								}
								?>
							</ul>
						</div>
					</div>
					<?php
					break;
				case 'VIEWS':
					?>
					<div id="p-views" role="navigation" class="vectorTabs<?php
					if ( count( $this->data['view_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-views-label">
						<h3 id="p-views-label"><?php $this->msg( 'views' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['view_urls'] as $link ) {
								?>
								<li<?php echo $link['attributes'] ?>><span><a href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" <?php
										echo $link['key'];
										if ( isset ( $link['rel'] ) ) {
											echo ' rel="' . htmlspecialchars( $link['rel'] ) . '"';
										}
										?>><?php
											// $link['text'] can be undefined - bug 27764
											if ( array_key_exists( 'text', $link ) ) {
												echo array_key_exists( 'img', $link )
													? '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />'
													: htmlspecialchars( $link['text'] );
											}
											?></a></span></li>
							<?php
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'ACTIONS':
					?>
					<div id="p-cactions" role="navigation" class="vectorMenu<?php
					if ( count( $this->data['action_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-cactions-label">
						<h3 id="p-cactions-label"><span><?php
							$this->msg( 'dgraph-more-actions' )
						?></span><a href="#"></a></h3>

						<div class="menu">
							<ul<?php $this->html( 'userlangattributes' ) ?>>
								<?php
								foreach ( $this->data['action_urls'] as $link ) {
									?>
									<li<?php echo $link['attributes'] ?>>
										<a href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" <?php
										echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] )
											?></a>
									</li>
								<?php
								}
								?>
							</ul>
						</div>
					</div>
					<?php
					break;
				case 'PERSONAL':
					?>
					<div id="p-personal" role="navigation" class="<?php
					if ( count( $this->data['personal_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-personal-label">
						<h3 id="p-personal-label"><?php $this->msg( 'personaltools' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							$personalTools = $this->getPersonalTools();
							foreach ( $personalTools as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'SEARCH':
					?>
					<div id="p-search" role="search">
						<h3<?php $this->html( 'userlangattributes' ) ?>>
							<label for="searchInput"><?php $this->msg( 'search' ) ?></label>
						</h3>

						<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
							<div id="simpleSearch">
							<?php
							echo $this->makeSearchInput( array( 'id' => 'searchInput' ) );
							echo Html::hidden( 'title', $this->get( 'searchtitle' ) );
							// We construct two buttons (for 'go' and 'fulltext' search modes),
							// but only one will be visible and actionable at a time (they are
							// overlaid on top of each other in CSS).
							// * Browsers will use the 'fulltext' one by default (as it's the
							//   first in tree-order), which is desirable when they are unable
							//   to show search suggestions (either due to being broken or
							//   having JavaScript turned off).
							// * The mediawiki.searchSuggest module, after doing tests for the
							//   broken browsers, removes the 'fulltext' button and handles
							//   'fulltext' search itself; this will reveal the 'go' button and
							//   cause it to be used.
							echo $this->makeSearchButton(
								'fulltext',
								array( 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' )
							);
							echo $this->makeSearchButton(
								'go',
								array( 'id' => 'searchButton', 'class' => 'searchButton' )
							);
							?>
							</div>
						</form>
					</div>
					<?php

					break;
			}
		}
	}
}
