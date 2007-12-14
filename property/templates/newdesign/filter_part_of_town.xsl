<!-- $Id: filter_part_of_town.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="filter_part_of_town">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="select_name_part_of_town"><xsl:value-of select="select_name_part_of_town"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>

		<form method="post" action="{$select_action}" class="menu" title="Part of town">
			<select name="{$select_name_part_of_town}">
				<option value=""><xsl:value-of select="lang_no_part_of_town"/></option>
				<xsl:apply-templates select="part_of_town_list"/>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</select>
		</form>
	</xsl:template>

	<xsl:template match="part_of_town_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
