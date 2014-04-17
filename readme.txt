=== WooCommerce Boleto ===
Contributors: claudiosanches, deblyn
Tags: woocommerce, boleto, banco do brasil, bradesco, caixa, hsbc, itau, nossa caixa, real, santander, unibanco
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Boleto as payment gateway in WooCommerce plugin

== Description ==

This plugin adds Boleto as payment gateway in [WooCommerce](http://wordpress.org/extend/plugins/woocommerce/) plugin.

Please notice that [WooCommerce](http://wordpress.org/extend/plugins/woocommerce/) must be installed and active.

### Descrição em Português: ###

Adicione **Boleto Bancário** como forma de pagamento em sua loja WooCommerce.

São aceitos boletos dos bancos:

* Banco do Brasil
* Bradesco
* Caixa Economica Federal
* HSBC
* Itau
* Nossa Caixa
* Real
* Santander
* Unibanco

O plugin WooCommerce Boleto foi desenvolvido sem nenhum incentivo de nenhum dos bancos citados a cima. Nenhum dos desenvolvedores deste plugin possuem vínculos com qualquer banco citado.

Este plugin foi desenvolvido usando o [BoletoPHP](http://boletophp.com.br/).

= Instalação: =

Confira o nosso guia de instalação e configuração do Boleto na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-boleto/installation/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-boleto/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-boleto) (apenas em inglês).
* Criando um *Issue* em nosso [fórum no GitHub](https://github.com/claudiosmweb/woocommerce-boleto/issues) (em português).

= Quer colaborar? =

Envie sugestões ou *Pull Requests* em [GitHub](https://github.com/claudiosmweb/woocommerce-boleto/).

= Créditos =

* Código fonte dos boletos => [BoletoPHP](http://boletophp.com.br/)
* Ícone do checkout => [Yummygum](http://yummygum.com/)

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Payment Gateways, choose **Boleto** and fill the options.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress;
* Ative o plugin;
* Depois navegue até WooCommerce -> Configurações -> Portais de Pagamento, selecione a opção **Boleto** e preencha as opções do plugin.

= Requerimentos: =

* WooCommerce 2.0.0 ou superior.
* Possuir convênio com sua agência bancária para emitir boletos.

= Configuração do Plugin: =

1. Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Portais de pagamento"  > "Boleto";
2. Habilite o opção **Boleto**;
3. Clique no link **Boleto** e configure as opções. Será necessário ter uma conta no banco e os dados necessários para preencher as opções;
4. Pronto, sua loja já pode receber pagamentos pelo Boleto.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce 2.0.0 or later installed and active.
* Own seller account in the bank.

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

Assim como os arquivos do [BoletoPHP](http://boletophp.com.br/). Cada arquivo do BoletoPHP possui um cabeçalho com os seus respectivos créditos.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce 2.0.0 ou mais recente.
* Possuir convênio com sua agência bancária para emitir boletos.

= Boleto recebe pagamentos de quais países? =

* Recebe apenas do Brasil.

= O plugin pode gerar Boletos de quais bancos? =

* Banco do Brasil
* Bradesco
* Caixa Economica Federal
* HSBC
* Itau
* Nossa Caixa
* Real
* Santander
* Unibanco

= Posso vender usando boletos de mais de um banco? =

Não!  
O plugin permite que você venda utilizando boletos de apenas um banco.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Crie um *Issue* em nosso [fórum no GitHub](https://github.com/claudiosmweb/woocommerce-boleto/issues) (em português).

= São aceitos arquivos de retorno? =

Infelizmente não.

= Quer colaborar? =

Envie sugestões ou *Pull Requests* em [GitHub](https://github.com/claudiosmweb/woocommerce-boleto/).

== Screenshots ==

1. Plugin settings.
2. Shop order metabox.

== Changelog ==

= 1.2.2 - 27/03/2013 =

* Melhoria/Correção do método de atualização, agora ele da flush nas urls para validar a página de boleto.

= 1.2.1 - 26/03/2013 =

* Correção de um erro fatal na ativação do plugin.

= 1.2.0 - 23/03/2013 =

* Melhorada a forma de abrir os boletos, foi removida a página e adicionada em seu lugar um endpoint, desta forma evitamos conflitos com alguns temas.

= 1.1.2 - 29/12/2013 =

* Correção do template do boleto da Caixa Econômica Federal - SIGCB.

= 1.1.1 - 26/12/2013 =

* Correção do boleto da Caixa Econômica Federal - SIGCB.

= 1.1.0 - 14/12/2013 =

* Corrigido padrões de código.
* Removida compatibilidade com versões 1.6.x ou inferiores do WooCommerce.
* Adicionada compatibilidade com WooCommerce 2.1 ou superior.
* Adicionada opção de carteira `25` do Bradesco.

= 1.0.1 - 13/10/2013 =

* Adicionada opção de carteira `09` do Bradesco.
* Correção na ordem das opções de carteira do Itau.
* Correção do metabox do boleto em *Pedidos*.

= 1.0.0 - 07/09/2013 =

* Correção das opções de todos os campos no plugin.
* Removida a opção de taxa do boleto.
* Adicionada opção para editar o demonstrativo e as instruções do boleto.
* Adicionado metabox com informações do boleto e para reenviar com nova data.

= 0.3 - 20/04/2013 =

* Trocada a consulta de ID do template boleto.php pela função `woocommerce_get_order_id_by_order_key` nativa do WooCommerce.

= 0.2 - 10/03/2013 =

* Adicionada compatibilidade com o WooCommerce 1.6.6.

= 0.1 =

* Versão incial do plugin.
* Versão beta!

== Upgrade Notice ==

= 1.2.2 =

* Melhorada a forma de abrir os boletos, foi removida a página e adicionada em seu lugar um endpoint, desta forma evitamos conflitos com alguns temas.
* Correção de um erro fatal na ativação do plugin.
* Melhoria/Correção do método de atualização, agora ele da flush nas urls para validar a página de boleto.

== License ==

WooCommerce Boleto is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce Boleto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce Boleto. If not, see <http://www.gnu.org/licenses/>.
