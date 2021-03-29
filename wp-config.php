<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', "landingseguralta2021" );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', "root" );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', "" );

/** Nome do host do MySQL */
define( 'DB_HOST', "localhost" );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Bu Yg!7<7|8ho^FR<~8OLe+2<?,(TlU_e?3K4iBN8&5[LL7WGv,S=CD$zqw<bFEh' );
define( 'SECURE_AUTH_KEY',  'OL[=BWVXaI1Qn`Cf5ISK7.[T6]k,SrLtR:fveA`aO{HdE%lV8q)cwIKs:b[^]z:7' );
define( 'LOGGED_IN_KEY',    '/$!`dO{<iIYjFcpd7&0bLXn+mGif#xaY>}[Q@*bJ>LJ<Ii$.Pp~}&[(60>En&Ali' );
define( 'NONCE_KEY',        '=RcF6pZ(p!NvE}Ih$L5tMM}T.bM2`P(gbi3!JdXHq~y`jlk$Cusi,6}v@<k$,d*0' );
define( 'AUTH_SALT',        'v{aPUa6?7&5|z$/Wk-qMmAf([4M(z TvU);i9r@^P*]$FP9my#-H9t?M{E:X3I<>' );
define( 'SECURE_AUTH_SALT', 'j*_ KvYi{VNR0:tZS%4`^rK_OWy#N0pPnL~$aEq[X>R:Y0~/58~_%60+o{J/wyy-' );
define( 'LOGGED_IN_SALT',   'I);>M8 _ $z85^oabuIB.]o[f T{ReAH7z;icHT<Y=HMKb?qnHu3$80L,agfhF#l' );
define( 'NONCE_SALT',       'R;(X+|1tv$}Iiw:A/n)V6{|s3za.iwVS:f*>jlmy]4kK9=R0x`kz-Wm[vklE2ow.' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';
