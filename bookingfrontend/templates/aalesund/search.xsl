<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		//		var selected_part_of_towns = "<xsl:value-of select="selected_part_of_towns"/>";
	</script>
	<div id="loading">
		<xsl:attribute name="title">
			<xsl:value-of select="php:function('lang', 'fetching data')" />
		</xsl:attribute>
		<p>
			<xsl:value-of select="php:function('lang', 'please wait')" />
		</p>
	</div>

	<div class="content">
		<a href="#" class="scrollup">
			<xsl:value-of select="php:function('lang', 'scroll to top')" />
		</a>

		<form method="GET" id="search">
			<input type="hidden" id="menuaction" name="menuaction" value="bookingfrontend.uisearch.index" />
			<!--input type="hidden" id="activity_top_level" name="activity_top_level" value="{activity_top_level}" /-->
		</form>
		<div class="pure-g">
			<div class="pure-u-1 pure-u-lg-1-2 search-line search-left-field">

				<div class="pure-u-1">
					<div class="heading">
						<!--<xsl:value-of select="php:function('lang', 'building')" /> /-->
						<b>Finn lokale</b>
					</div>
					<div id="building_container" class="search-building">
						<input id="field_building_id" name="building_id" type="hidden">
							<xsl:attribute name="value">
								<xsl:value-of select="building_id"/>
							</xsl:attribute>
						</input>
						<input id="field_building_name" name="building_name" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="building_name"/>
							</xsl:attribute>
							<xsl:attribute name="placeholder">
								<xsl:text> Søk etter lokale/bygning/anlegg</xsl:text>
							</xsl:attribute>

						</input>
						<input id="submit_building" type="button"/>
						<xsl:text> </xsl:text>

						<a id="reset" title="Reset" href="#">
							<xsl:value-of select="php:function('lang', 'reset')"/>
						</a>
					</div>

				</div>
				<div class="pure-u-1">

					<div class="search-searchterm">
						<input id="field_searchterm"  name="searchterm" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="searchterm"/>
							</xsl:attribute>
							<xsl:attribute name="placeholder">
								<xsl:text> Søk i fritekst</xsl:text>
							</xsl:attribute>
						</input>

						<input id="submit_searchterm" type="button"/>
					</div>
				</div>
				<div class="pure-u-1 select-box">
					<div class="heading">
						<xsl:value-of select="php:function('lang', 'part of town')" />
					</div>
					<ul id="part_of_town">
						<xsl:for-each select="part_of_towns">
							<li>
								<label class="control control--checkbox">
									<input  type="checkbox" name="part_of_town[]">
										<xsl:attribute name="value">
											<xsl:value-of select="id"/>
										</xsl:attribute>
										<xsl:if test="checked = 1">
											<xsl:attribute name="checked">
												<xsl:text>checked</xsl:text>
											</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="name"/>
									<div class="control__indicator"></div>
								</label>
							</li>
						</xsl:for-each>
					</ul>
				</div>
				<div class="pure-u-1 select-box">
					<div class="heading">
						<!--xsl:value-of select="php:function('lang', 'Activity')" /-->
						Velg hovedkategori/avdeling
					</div>
					<ul id="top_level">
						<xsl:for-each select="top_levels">
							<li>
								<label class="control control--checkbox">
									<input type="checkbox" name="top_levels[]">
										<xsl:attribute name="value">
											<xsl:value-of select="id"/>
										</xsl:attribute>
										<xsl:attribute name="id">
											<xsl:value-of select="location"/>
										</xsl:attribute>
										<xsl:if test="checked = 1">
											<xsl:attribute name="checked">
												<xsl:text>checked</xsl:text>
											</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="name"/>
									<div class="control__indicator"></div>
								</label>
							</li>
						</xsl:for-each>
					</ul>
				</div>

				<!--div class="pure-u-1" id="activity_tree">
					<div class="heading">
						Velg type lokale/anlegg/utstyr.
					</div>
					<style>
						#expandcontractdiv {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
						#treeDiv1 { background: #fff; padding:1em; margin-top:1em; }
						.no_checkbox>i.jstree-checkbox{ display:none}
					</style>
					<script type="text/javascript">
						filter_tree = <xsl:value-of select="filter_tree"/>;
					</script>
					<div id="treecontrol">
						<a id="collapse1" title="Collapse the entire tree below" href="#">
							<xsl:value-of select="php:function('lang', 'collapse all')"/>
						</a>
						<xsl:text> | </xsl:text>
						<a id="expand1" title="Expand the entire tree below" href="#">
							<xsl:value-of select="php:function('lang', 'expand all')"/>
						</a>
					</div>
					<div id="treeDiv1"></div>
				</div-->

				<div class="pure-u-1 select-box">
					<div class="heading">
						<!--xsl:value-of select="php:function('lang', 'type')" /-->
						Vis kun treff som er:
					</div>
					<ul id="search_type">
						<li>
							<label class="control control--checkbox">
								<input type="checkbox" name="search_type[]" value="building"/>
								<xsl:value-of select="php:function('lang', 'building')" />
								<div class="control__indicator"></div>
							</label>
						</li>
						<li>
							<label class="control control--checkbox">
								<input type="checkbox" name="search_type[]" value="resource"/>
								<xsl:value-of select="php:function('lang', 'resource')" />
								<div class="control__indicator"></div>
							</label>
						</li>
						<li>
							<label class="control control--checkbox">
								<input type="checkbox" name="search_type[]" value="organization"/>
								<xsl:value-of select="php:function('lang', 'organization')" />
								<div class="control__indicator"></div>
							</label>
						</li>
						<li>
							<label class="control control--checkbox">
								<input type="checkbox" name="search_type[]" value="event"/>
								<xsl:value-of select="php:function('lang', 'event')" />
								<div class="control__indicator"></div>
							</label>
						</li>
					</ul>
				</div>

			</div>
			<div class="pure-u-1 pure-u-lg-1-2 search-right-field">
				<div id = "total_records_top"></div>
				<div id="result"></div>
			</div>

		</div>

	</div>
</xsl:template>
