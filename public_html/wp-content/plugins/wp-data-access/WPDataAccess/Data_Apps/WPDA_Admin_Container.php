<?php

namespace WPDataAccess\Data_Apps;

use  WPDataAccess\API\WPDA_API ;
use  WPDataAccess\WPDA ;
class WPDA_Admin_Container
{
    private  $dbs = '' ;
    private  $tbl = '' ;
    public function __construct( $dbs, $tbl )
    {
        if ( !current_user_can( 'manage_options' ) ) {
            throw new \Exception( __( 'ERROR: Not authorized', 'wp-data-access' ) );
        }
        $this->dbs = $dbs;
        $this->tbl = $tbl;
    }
    
    public function show()
    {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        $script_url = plugin_dir_url( __DIR__ ) . '../assets/dist/main.js';
        ?>

			<div class="wpda-yd-container">
				<div
					class="yd-container"
					data-source="{ 'dbs': '<?php 
        echo  esc_attr( $this->dbs ) ;
        ?>', 'tbl': '<?php 
        echo  esc_attr( $this->tbl ) ;
        ?>' }"
				></div>
			</div>
			<script>
				window.YD_APP_CONFIG = {
					urlRoot: "<?php 
        echo  esc_url( get_rest_url() ) . WPDA_API::WPDA_NAMESPACE . '/' ;
        ?>",
					appDebug: <?php 
        echo  ( 'on' === WPDA::get_option( WPDA::OPTION_PLUGIN_DEBUG ) ? 'true' : 'false' ) ;
        ?>
				}
			</script>
			<script type="module" src="<?php 
        echo  esc_attr( $script_url ) ;
        ?>"></script>

			<?php 
        $this->ccs();
    }
    
    private function ccs()
    {
        ?>

			<style>
                .wpda-yd-container .yd-container .dataTables_wrapper .dt-bulk-actions select,
                .wpda-yd-container .yd-container .dataTables_wrapper .dataTables_length select {
					padding: 0 24px 0 8px;
                }

                .wpda-yd-container .yd-container .dataTables_wrapper .dt-bulk-actions select option,
                .wpda-yd-container .yd-container .dataTables_wrapper .dataTables_length select option {
                    padding: 0 24px 0 8px;
                }

                .wpda-yd-container .yd-container i.fa-solid {
                    font-size: 16px;
                }

                .wpda-yd-container .yd-container .dataTables_wrapper th.yd-actions input[type="checkbox"].yd-select-row {
                    margin-top: -1px;
					margin-bottom: 0;
                }

                .wpda-yd-container .yd-container .dataTables_wrapper td.yd-actions input[type="checkbox"].yd-select-row {
                    margin-top: 0;
                    margin-bottom: 0;
                }

                .wpda-yd-container .yd-form input[type=date],
                .wpda-yd-container .yd-form input[type=datetime-local],
                .wpda-yd-container .yd-form input[type=datetime],
                .wpda-yd-container .yd-form input[type=email],
                .wpda-yd-container .yd-form input[type=month],
                .wpda-yd-container .yd-form input[type=number],
                .wpda-yd-container .yd-form input[type=password],
                .wpda-yd-container .yd-form input[type=search],
                .wpda-yd-container .yd-form input[type=tel],
                .wpda-yd-container .yd-form input[type=text],
                .wpda-yd-container .yd-form input[type=time],
                .wpda-yd-container .yd-form input[type=url],
                .wpda-yd-container .yd-form input[type=week] {
                    border: 0;
					padding: 16.5px 14px;
					box-shadow: none;
                }

				<?php 
        
        if ( is_admin() ) {
            // Take admin header into account for drawer positioning
            ?>
					#yd-setting-drawer-root .MuiPaper-root.MuiDrawer-paper {
						top: 32px !important;
						height: calc(100% - 32px - 8px) !important;
					}
					<?php 
        } else {
            ?>
					#yd-setting-drawer-root .MuiPaper-root.MuiDrawer-paper {
						height: 100% !important;
					}
					<?php 
        }
        
        ?>

                #yd-setting-drawer-root .MuiPaper-root.MuiDrawer-paper input {
                    border: 0;
                    padding: 16.5px 14px;
                    box-shadow: none;
				}

                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="checkbox"].disabled,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="checkbox"].disabled:checked::before,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="checkbox"]:disabled,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="checkbox"]:disabled:checked::before,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="radio"].disabled,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="radio"].disabled:checked::before,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="radio"]:disabled,
                #yd-setting-drawer.MuiPaper-root.MuiDrawer-paper input[type="radio"]:disabled:checked::before {
					opacity: 0;
				}

                .wpda-yd-container .yd-container .yd-table .yd-inline-search-item {
                    height: 40px;
					border: 1px solid #aaa;
                }

                .wpda-yd-container .yd-container .yd-table select.yd-inline-search-item {
					padding-right: 25px;
				}

                .wpda-yd-container .yd-container .yd-inline-search-item {
					width: min-content;
				}

                .wpda-yd-container .yd-container table.yd-data-table.dataTable {
					margin: 0;
					border: 0;
				}
			</style>

			<?php 
    }

}