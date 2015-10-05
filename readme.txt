=== WooCommerce Boleto ===
Contributors: claudiosanches, deblynprado
Tags: woocommerce, boleto, banco do brasil, bradesco, caixa, hsbc, itau, nossa caixa, real, santander, unibanco, bancoob
Requires at least: 3.9
Tested up to: 4.3
Stable tag: 1.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Boleto as payment gateway in WooCommerce plugin

== Description ==

Adicione **Boleto Bancário** como forma de pagamento em sua loja WooCommerce.

São aceitos boletos dos bancos:

* Bancoob
* Banco do Brasil
* Bradesco
* Caixa Economica Federal
* HSBC
* Itaú (recomendado utilizar o plugin [WooCommerce Itau Shopline](https://wordpress.org/plugins/wc-itau-shopline/) no lugar)
* Nossa Caixa
* Real
* Santander
* Unibanco

O plugin WooCommerce Boleto foi desenvolvido sem nenhum incentivo de nenhum dos bancos citados a cima. Nenhum dos desenvolvedores deste plugin possuem vínculos com qualquer banco citado.

Este plugin foi desenvolvido usando o [BoletoPHP](http://boletophp.com.br/).

= Compatibilidade =

* [WooCommerce](https://wordpress.org/plugins/woocommerce) - Para as versões 2.2.x, 2.3.x ou 2.4.x.
* [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) - Para preenchimento do endereço e CPF/CNPJ no boleto.
* [WPML/WooCommerce Multilingual](https://wordpress.org/plugins/woocommerce-multilingual/).

= Instalação: =

Confira o nosso guia de instalação e configuração do Boleto na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-boleto/installation/).

= Add-ons =

* [Gerar boletos em PDF](https://wordpress.org/plugins/wc-boleto-pdf/).

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

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress;
* Ative o plugin;
* Depois navegue até WooCommerce -> Configurações -> Portais de Pagamento, selecione a opção **Boleto** e preencha as opções do plugin.

= Requerimentos: =

* WooCommerce 2.2.0 ou superior.
* Possuir convênio com sua agência bancária para emitir boletos.

= Configuração do Plugin: =

1. Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Finalizar compra" > "Boleto";
2. Habilite a opção **Boleto**;
3. Clique no link **Boleto** e configure as opções. Será necessário ter uma conta no banco e os dados necessários para preencher as opções;
4. Pronto, sua loja já pode receber pagamentos pelo Boleto.

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

Assim como os arquivos do [BoletoPHP](http://boletophp.com.br/). Cada arquivo do BoletoPHP possui um cabeçalho com os seus respectivos créditos.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce 2.2.0 ou mais recente.
* Possuir convênio com sua agência bancária para emitir boletos.

= Boleto recebe pagamentos de quais países? =

* Recebe apenas do Brasil.

= O plugin pode gerar Boletos de quais bancos? =

* Bancoob
* Banco do Brasil
* Bradesco
* Caixa Economica Federal
* HSBC
* Itaú (recomendado utilizar o plugin [WooCommerce Itau Shopline](https://wordpress.org/plugins/wc-itau-shopline/) no lugar)
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

= Como posso exibir os boletos no formato PDF? =

É possível gerar os boletos no formato PDF utilizando o plugin [WooCommerce Boleto - PDF Add-on](https://wordpress.org/plugins/wc-boleto-pdf/).

= Quer colaborar? =

Envie sugestões ou *Pull Requests* em [GitHub](https://github.com/claudiosmweb/woocommerce-boleto/).

== Screenshots ==

1. Configurações do plugin.
2. Metabox onde é possível pegar o link do boleto e também alterar a data de vencimento.

== Changelog ==

= 1.5.3 - 2015/10/04 =

* Adicionado action para ajudar a utilização do plugin "WooCommerce Boleto - PDF Add-on".
* Corrigido filtro `add_filter`.

= 1.5.2 - 2015/09/05 =

* Corrigida a prioridade do método que exibe os templates dos boletos (isso corrige problemas de boletos não abrindo em alguns temas).

= 1.5.1 - 2015/04/21 =

* Correção na exibição das URLs dos boletos.

= 1.5.0 - 2015/04/17 =

* Adicionado suporte para boletos do banco Bancoob (obrigado [douglastycho](https://github.com/douglastycho)).
* Alterado as nomenclaturas de CEDENTE para BENEFICIÁRIO e de SACADO para PAGADOR para tornar o plugin complacente com as mudanças da lei 3.656 do Banco Central.
* Adicionado CPF ou CNPJ nas informações do beneficiário para tornar o plugin complacente com as mudanças da lei 3.656 do Banco Central.
* Forçada a exibição do botão do boleto após o pagamento (alguns temas oultam o botão).
* Criado filtro woocommerce_boleto_url.
* Adicionada função wc_boleto_get_boleto_url() que recupera a URL do boleto com base na chave do pedido.
* Adicionada função wc_boleto_get_boleto_url_by_order_id() que recupera a URL do boleto com base no ID do pedido.
* Adicionada compatibilidade com o plugin WooCommerce Extra Checkout Fields para que seja possível pegar CPF e CNPJ dos clientes.
* Corrigido problemas de exibição dos boletos que era causado em alguns temas.

= 1.4.1 - 2014/07/27 =

* Melhoria na função que gera a página do boleto.

= 1.4.0 - 2014/06/25 =

* Permitido criar pedidos pelo adminstrador e usar a opção de boleto.

= 1.3.0 - 2014/06/24 =

* Melhoria no valor do boleto, agora ele não é mais salvo na hora do pedido e permite que seja alterado o valor junto com o pedido (caso o pedido seja alterado pelo administrador).
* Melhoria na exibição do endereço no boleto.
* Melhorias gerais no código do plugin.

= 1.2.2 - 2014/03/27 =

* Melhoria/Correção do método de atualização, agora ele da flush nas urls para validar a página de boleto.

= 1.2.1 - 2014/03/26 =

* Correção de um erro fatal na ativação do plugin.

= 1.2.0 - 2014/03/23 =

* Melhorada a forma de abrir os boletos, foi removida a página e adicionada em seu lugar um endpoint, desta forma evitamos conflitos com alguns temas.

= 1.1.2 - 2013/12/29 =

* Correção do template do boleto da Caixa Econômica Federal - SIGCB.

= 1.1.1 - 2013/12/26 =

* Correção do boleto da Caixa Econômica Federal - SIGCB.

= 1.1.0 - 2013/12/14 =

* Corrigido padrões de código.
* Removida compatibilidade com versões 1.6.x ou inferiores do WooCommerce.
* Adicionada compatibilidade com WooCommerce 2.1 ou superior.
* Adicionada opção de carteira `25` do Bradesco.

= 1.0.1 - 2013/10/13 =

* Adicionada opção de carteira `09` do Bradesco.
* Correção na ordem das opções de carteira do Itau.
* Correção do metabox do boleto em *Pedidos*.

= 1.0.0 - 2013/09/07 =

* Correção das opções de todos os campos no plugin.
* Removida a opção de taxa do boleto.
* Adicionada opção para editar o demonstrativo e as instruções do boleto.
* Adicionado metabox com informações do boleto e para reenviar com nova data.

= 0.3 - 2013/04/20 =

* Trocada a consulta de ID do template boleto.php pela função `woocommerce_get_order_id_by_order_key` nativa do WooCommerce.

= 0.2 - 2013/03/10 =

* Adicionada compatibilidade com o WooCommerce 1.6.6.

= 0.1 =

* Versão incial do plugin.
* Versão beta!

== Upgrade Notice ==

= 1.5.3 =

* Adicionado action para ajudar a utilização do plugin "WooCommerce Boleto - PDF Add-on".
* Corrigido filtro `add_filter`.

== License ==

WooCommerce Boleto is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce Boleto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce Boleto. If not, see <http://www.gnu.org/licenses/>.
