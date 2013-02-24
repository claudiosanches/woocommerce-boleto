<?php
/**
 * BoletoPhp - Versão Beta.
 *
 * Licença: GPL <http://www.gnu.org/licenses/gpl.txt>.
 *
 * Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel
 * William Schultz e Leandro Maniezo que por sua vez foi derivado do
 * PHPBoleto de João Prado Maia e Pablo Martins F. Costa.
 *
 * Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)
 * Acesse o site do Projeto BoletoPhp: www.boletophp.com.br.
 *
 * Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>.
 * Desenvolvimento Boleto Itaú: Glauber Portella.
 *
 * Este documento é um fork criado para funcionar no WooCommerce.
 * Você pode ver mais detalhe sobre o projeto em <https://github.com/wpbrasil/woocommerce-boleto>.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $dadosboleto["identificacao"]; ?></title>
        <meta charset="utf-8">
        <meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL">
        <link rel="stylesheet" href="<?php echo wcboleto_assets_url(); ?>css/boleto.css" />
    </head>
    <body>
        <table>
            <tr>
                <td valign="top" class="cp">
                    <p style="text-align: center;"><?php _e( 'Instru&ccedil;&otilde;es de Impress&atilde;o', 'wcboleto' ); ?></p>
                </td>
            </tr>
            <tr>
                <td valign="top" class="cp">
                    <ul>
                        <li><?php _e( 'Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (N&atilde;o use modo econ&ocirc;mico).', 'wcboleto' ); ?></li>
                        <li><?php _e( 'Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens m&iacute;nimas &agrave; esquerda e &agrave; direita do formul&aacute;rio.', 'wcboleto' ); ?></li>
                        <li><?php _e( 'Corte na linha indicada. N&atilde;o rasure, risque, fure ou dobre a regi&atilde;o onde se encontra o c&oacute;digo de barras.', 'wcboleto' ); ?></li>
                        <li><?php _e( 'Caso n&atilde;o apare&ccedil;a o c&oacute;digo de barras no final, clique em F5 para atualizar esta tela.', 'wcboleto' ); ?></li>
                        <li><?php _e( 'Caso tenha problemas ao imprimir, copie a seq&uuml;encia num&eacute;rica abaixo e pague no caixa eletr&ocirc;nico ou no internet banking:', 'wcboleto' ); ?><br /><br />
                            <span class="ld2"><?php _e( 'Linha Digit&aacute;vel:', 'wcboleto' ); ?> <?php echo $dadosboleto['linha_digitavel']; ?><br />
                            <?php _e( 'Valor: R$', 'wcboleto' ); ?> <?php echo $dadosboleto['valor_boleto']; ?></span></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="ct">
                    <img style="height: 1px width: 666px;" src="<?php echo wcboleto_assets_url(); ?>images/6.png" alt="" />
                </td>
            </tr>
            <tr>
                <td class="ct">
                    <p class="cp" style="text-align: right;"><?php _e( 'Recibo do Sacado', 'wcboleto' ); ?></p>
                </td>
            </tr>
        </table>

        <table id="branding">
            <tr>
                <td style="width: 170px;">
                    <img src="<?php echo wcboleto_assets_url(); ?>images/logo_empresa.png" alt="<?php bloginfo('name'); ?>" />
                </td>
                <td valign="top">
                    <p class="ti"><?php echo $dadosboleto['identificacao']; ?> <?php echo isset( $dadosboleto['cpf_cnpj'] ) ? '<br />' . $dadosboleto['cpf_cnpj'] : '' ?><br /><?php echo $dadosboleto['endereco']; ?><br /><?php echo $dadosboleto['cidade_uf']; ?></p>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="cp" style="width: 150px; vertical-align: bottom;">
                    <img src="<?php echo wcboleto_assets_url(); ?>images/logoitau.jpg" alt="Itau" width="150" height="40" style="display: block;">
                </td>
                <td style="width 3px; vertical-align: bottom;">
                    <img src="<?php echo wcboleto_assets_url(); ?>images/3.png" alt="" width="2" height="22">
                </td>
                <td class="cpt" style="width 58px; vertical-align: bottom;">
                    <p class="bc" style="text-align: center;"><?php echo $dadosboleto['codigo_banco_com_dv']; ?></p>
                </td>
                <td style="width 3px; vertical-align: bottom;">
                    <img src="<?php echo wcboleto_assets_url(); ?>images/3.png" alt="" width="2" height="22">
                </td>
                <td style="width 452px; vertical-align: bottom; text-align: right">
                    <span class="ld"><?php echo $dadosboleto['linha_digitavel']; ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <img src="<?php echo wcboleto_assets_url(); ?>images/2.png" alt="" height="2" width="666">
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="ct" valign="top" width="7" height="13">
                    <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="ct" valign="top" width="298" height="13">
                    <span><?php _e( 'Cedente', 'wcboleto' ); ?></span>
                </td>
                <td class="ct" valign="top" width="7" height="13">
                    <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="ct" valign="top" width="126" height="13">
                    <span><?php _e( 'Ag&ecirc;ncia/C&oacute;digo do Cedente', 'wcboleto' ); ?></span>
                </td>
                <td class="ct" valign="top" width="7" height="13">
                    <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="ct" valign="top" width="34" height="13">
                    <span><?php _e( 'Esp&eacute;cie', 'wcboleto' ); ?></span>
                </td>
                <td class="ct" valign="top" width="7" height="13">
                    <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="ct" valign="top" width="53" height="13">
                    <span><?php _e( 'Quantidade', 'wcboleto' ); ?></span>
                </td>
                <td class="ct" valign="top" width="7" height="13">
                    <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="ct" valign="top" width="120" height="13">
                    <span><?php _e( 'Nosso n&uacute;mero', 'wcboleto' ); ?></span>
                </td>
            </tr>
            <tr>
                <td class="cp" valign="top" width="7" height="12">
                    <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="cp" valign="top" width="298" height="12">
                    <span><?php echo $dadosboleto["cedente"]; ?></span>
                </td>
                <td class="cp" valign="top" width="7" height="12">
                    <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="cp" valign="top" width="126" height="12">
                    <span><?php echo $dadosboleto["agencia_codigo"]?></span>
                </td>
                <td class="cp" valign="top" width="7" height="12">
                    <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="cp" valign="top" width="34" height="12">
                    <span><?php echo $dadosboleto["especie"]?></span>
                </td>
                <td class="cp" valign="top" width="7" height="12">
                    <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="cp" valign="top" width="53" height="12">
                    <span><?php echo $dadosboleto["quantidade"]?></span>
                </td>
                <td class="cp" valign="top" width="7" height="12">
                    <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                </td>
                <td class="cp" valign="top" align="right" width="120" height="12">
                    <span><?php echo $dadosboleto["nosso_numero"]?></span>
                </td>
            </tr>
            <tr>
                <td valign="top" width="7" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                </td>
                <td valign="top" width="298" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="298" border="0">
                </td>
                <td valign="top" width="7" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                </td>
                <td valign="top" width="126" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="126" border="0">
                </td>
                <td valign="top" width="7" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                </td>
                <td valign="top" width="34" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="34" border="0">
                </td>
                <td valign="top" width="7" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                </td>
                <td valign="top" width="53" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="53" border="0">
                </td>
                <td valign="top" width="7" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                </td>
                <td valign="top" width="120" height="1">
                    <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="120" border="0">
                </td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" colspan="3" height="13">
                        <span><?php _e( 'N&uacute;mero do documento', 'wcboleto' ); ?></span>
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="132" height="13">
                        <span><?php _e( 'CPF/CNPJ', 'wcboleto' ); ?></span>
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="134" height="13">
                        <span><?php _e( 'Vencimento', 'wcboleto' ); ?></span>
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        <span><?php _e( 'Valor documento', 'wcboleto' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" colspan="3" height="12">
                        <span><?php echo $dadosboleto["numero_documento"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="132" height="12">
                        <span><?php echo $dadosboleto["cpf_cnpj"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="134" height="12">
                        <span><?php echo $dadosboleto["data_vencimento"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="180" height="12">
                        <span><?php echo $dadosboleto["valor_boleto"]?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="113" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="113" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="72" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="72" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="132" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="132" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="134" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="134" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="113" height="13">
                        (-) Desconto / Abatimentos
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="112" height="13">
                        (-) Outras deduções
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="113" height="13">
                        (+) Mora / Multa
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="113" height="13">
                        (+) Outros acréscimos
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        (=) Valor cobrado
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="113" height="12"></td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="112" height="12"></td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="113" height="12"></td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="113" height="12"></td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="180" height="12"></td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="113" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="113" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="112" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="112" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="113" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="113" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="113" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="113" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="659" height="13">
                        Sacado
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="659" height="12">
                        <span><?php echo $dadosboleto["sacado"]?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="659" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="659" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" width="7" height="12"></td>
                    <td class="ct" width="564">
                        Demonstrativo
                    </td>
                    <td class="ct" width="7" height="12"></td>
                    <td class="ct" width="88">
                        Autenticação mecânica
                    </td>
                </tr>
                <tr>
                    <td width="7"></td>
                    <td class="cp" width="564">
                        <span><?php echo $dadosboleto["demonstrativo1"]?><br />
                        <?php echo $dadosboleto["demonstrativo2"]?><br />
                        <?php echo $dadosboleto["demonstrativo3"]?><br /></span>
                    </td>
                    <td width="7"></td>
                    <td width="88"></td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tbody>
                <tr>
                    <td width="7"></td>
                    <td width="500" class="cp">
                        <br />
                        <br />
                        <br />
                    </td>
                    <td width="159"></td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tr>
                <td class="ct" width="666"></td>
            </tr>
            <tbody>
                <tr>
                    <td class="ct" width="666">
                        <div align="right">
                            Corte na linha pontilhada
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="ct" width="666">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/6.png" width="665" border="0">
                    </td>
                </tr>
            </tbody>
        </table><br />
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tr>
                <td class="cp" width="150">
                    <span><img src="<?php echo wcboleto_assets_url(); ?>images/logoitau.jpg" width="150" height="40" border="0"></span>
                </td>
                <td width="3" valign="bottom">
                    <img height="22" src="<?php echo wcboleto_assets_url(); ?>images/3.png" width="2" border="0">
                </td>
                <td class="cpt" width="58" valign="bottom">
                    <div align="center">
                        <font class="bc"><?php echo $dadosboleto["codigo_banco_com_dv"]?></font>
                    </div>
                </td>
                <td width="3" valign="bottom">
                    <img height="22" src="<?php echo wcboleto_assets_url(); ?>images/3.png" width="2" border="0">
                </td>
                <td class="ld" align="right" width="453" valign="bottom">
                    <span class="ld"><span class="campotitulo"><?php echo $dadosboleto["linha_digitavel"]?></span></span>
                </td>
            </tr>
            <tbody>
                <tr>
                    <td colspan="5">
                        <img height="2" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="666" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="472" height="13">
                        Local de pagamento
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        Vencimento
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="472" height="12">
                        Pagável em qualquer Banco até o vencimento
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="180" height="12">
                        <span><?php echo $dadosboleto["data_vencimento"]?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="472" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="472" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="472" height="13">
                        Cedente
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        Agência/Código cedente
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="472" height="12">
                        <span><?php echo $dadosboleto["cedente"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="180" height="12">
                        <span><?php echo $dadosboleto["agencia_codigo"]?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="472" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="472" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="113" height="13">
                        Data do documento
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="153" height="13">
                        N<u>o</u> documento
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="62" height="13">
                        Espécie doc.
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="34" height="13">
                        Aceite
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="82" height="13">
                        Data processamento
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        Nosso número
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="113" height="12">
                        <div align="left">
                            <span><?php echo $dadosboleto["data_documento"]?></span>
                        </div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="153" height="12">
                        <span><?php echo $dadosboleto["numero_documento"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="62" height="12">
                        <div align="left">
                            <span><?php echo $dadosboleto["especie_doc"]?></span>
                        </div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="34" height="12">
                        <div align="left">
                            <span><?php echo $dadosboleto["aceite"]?></span>
                        </div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="82" height="12">
                        <div align="left">
                            <span><?php echo $dadosboleto["data_processamento"]?></span>
                        </div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="180" height="12">
                        <span><?php echo $dadosboleto["nosso_numero"]?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="113" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="113" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="153" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="153" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="62" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="62" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="34" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="34" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="82" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="82" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" colspan="3" height="13">
                        Uso do banco
                    </td>
                    <td class="ct" valign="top" height="13" width="7">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="83" height="13">
                        Carteira
                    </td>
                    <td class="ct" valign="top" height="13" width="7">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="53" height="13">
                        Espécie
                    </td>
                    <td class="ct" valign="top" height="13" width="7">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="123" height="13">
                        Quantidade
                    </td>
                    <td class="ct" valign="top" height="13" width="7">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="72" height="13">
                        Valor Documento
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        (=) Valor documento
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td valign="top" class="cp" height="12" colspan="3">
                        <div align="left"></div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="83">
                        <div align="left">
                            <span><?php echo $dadosboleto["carteira"]?></span>
                        </div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="53">
                        <div align="left">
                            <span><?php echo $dadosboleto["especie"]?></span>
                        </div>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="123">
                        <span><?php echo $dadosboleto["quantidade"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="72">
                        <span><?php echo $dadosboleto["valor_unitario"]?></span>
                    </td>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" align="right" width="180" height="12">
                        <span><?php echo $dadosboleto["valor_boleto"]?></span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="75" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="31" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="31" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="83" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="83" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="53" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="53" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="123" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="123" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="72" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="72" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tbody>
                <tr>
                    <td align="right" width="10">
                        <table cellspacing="0" cellpadding="0" border="0" align="left">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="1" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td valign="top" width="468" rowspan="5">
                        <font class="ct">Instruções (Texto de responsabilidade do cedente)</font><br />
                        <br />
                        <span class="cp"><font><?php echo $dadosboleto["instrucoes1"]; ?><br />
                        <?php echo $dadosboleto["instrucoes2"]; ?><br />
                        <?php echo $dadosboleto["instrucoes3"]; ?><br />
                        <?php echo $dadosboleto["instrucoes4"]; ?></font><br />
                        <br /></span>
                    </td>
                    <td align="right" width="188">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="ct" valign="top" width="180" height="13">
                                        (-) Desconto / Abatimentos
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="cp" valign="top" align="right" width="180" height="12"></td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                                    </td>
                                    <td valign="top" width="180" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="10">
                        <table cellspacing="0" cellpadding="0" border="0" align="left">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="1" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td align="right" width="188">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="ct" valign="top" width="180" height="13">
                                        (-) Outras deduções
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="cp" valign="top" align="right" width="180" height="12"></td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                                    </td>
                                    <td valign="top" width="180" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="10">
                        <table cellspacing="0" cellpadding="0" border="0" align="left">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="1" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td align="right" width="188">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="ct" valign="top" width="180" height="13">
                                        (+) Mora / Multa
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="cp" valign="top" align="right" width="180" height="12"></td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                                    </td>
                                    <td valign="top" width="180" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="10">
                        <table cellspacing="0" cellpadding="0" border="0" align="left">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="1" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td align="right" width="188">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="ct" valign="top" width="180" height="13">
                                        (+) Outros acréscimos
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="cp" valign="top" align="right" width="180" height="12"></td>
                                </tr>
                                <tr>
                                    <td valign="top" width="7" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                                    </td>
                                    <td valign="top" width="180" height="1">
                                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="10">
                        <table cellspacing="0" cellpadding="0" border="0" align="left">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td align="right" width="188">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                                <tr>
                                    <td class="ct" valign="top" width="7" height="13">
                                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="ct" valign="top" width="180" height="13">
                                        (=) Valor cobrado
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cp" valign="top" width="7" height="12">
                                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                                    </td>
                                    <td class="cp" valign="top" align="right" width="180" height="12"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tbody>
                <tr>
                    <td valign="top" width="666" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="666" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="659" height="13">
                        Sacado
                    </td>
                </tr>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="659" height="12">
                        <span><?php echo $dadosboleto["sacado"]?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="cp" valign="top" width="7" height="12">
                        <img height="12" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="659" height="12">
                        <span><?php echo $dadosboleto["endereco1"]?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="cp" valign="top" width="472" height="13">
                        <span><?php echo $dadosboleto["endereco2"]?></span>
                    </td>
                    <td class="ct" valign="top" width="7" height="13">
                        <img height="13" src="<?php echo wcboleto_assets_url(); ?>images/1.png" width="1" border="0">
                    </td>
                    <td class="ct" valign="top" width="180" height="13">
                        Cód. baixa
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="472" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="472" border="0">
                    </td>
                    <td valign="top" width="7" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="7" border="0">
                    </td>
                    <td valign="top" width="180" height="1">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/2.png" width="180" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="0" width="666">
            <tbody>
                <tr>
                    <td class="ct" width="7" height="12"></td>
                    <td class="ct" width="409">
                        Sacador/Avalista
                    </td>
                    <td class="ct" width="250">
                        <div align="right">
                            Autenticação mecânica - <b class="cp">Ficha de Compensação</b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="ct" colspan="3"></td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tbody>
                <tr>
                    <td valign="bottom" align="left" height="50">
                        <?php fbarcode($dadosboleto["codigo_barras"]); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="666" border="0">
            <tr>
                <td class="ct" width="666"></td>
            </tr>
            <tbody>
                <tr>
                    <td class="ct" width="666">
                        <div align="right">
                            Corte na linha pontilhada
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="ct" width="666">
                        <img height="1" src="<?php echo wcboleto_assets_url(); ?>images/6.png" width="665" border="0">
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>