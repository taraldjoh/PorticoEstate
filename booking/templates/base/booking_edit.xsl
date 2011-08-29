<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

        <ul class="pathway">
            <li><xsl:value-of select="php:function('lang', 'Bookings')" /></li>
            <li><a href="">#<xsl:value-of select="booking/id"/></a></li>
        </ul>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
		<dl class="form-col">
            <dt><label><xsl:value-of select="php:function('lang', 'Application')"/></label></dt>
            <dd>
				<xsl:if test="booking/application_id != ''">
					<a href="{booking/application_link}">#<xsl:value-of select="booking/application_id"/></a>
				</xsl:if>
            </dd>
		</dl>
		<div class="clr"/>
        <dl class="form-col">
            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="booking/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Active')"/>
                    </option>
                    <option value="0">
                    	<xsl:if test="booking/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                    </option>
                </select>
            </dd>
			<dt><label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
			<dd>
				<select name="activity_id" id="field_activity">
					<option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
					<xsl:for-each select="activities">
						<option>
							<xsl:if test="../booking/activity_id = id">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
							<xsl:value-of select="name"/>
						</option>
					</xsl:for-each>
				</select>
			</dd>
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')"/></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_building_id" name="building_id" type="hidden" value="{booking/building_id}"/>
                    <input id="field_building_name" name="building_name" type="text" value="{booking/building_name}"/>
                    <div id="building_container"/>
                </div>
            </dd>
            <dt><label for="field_season"><xsl:value-of select="php:function('lang', 'Season')"/></label></dt>
            <dd>
                <div id="season_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
            </dd>
            <dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')"/></label></dt>
            <dd>
                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
            </dd>
        </dl>
        <dl class="form-col">
            <dt style="margin-top: 100px;"><label for="field_group"><xsl:value-of select="php:function('lang', 'Organization')"/></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_org_id" name="organization_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="booking/organization_id"/></xsl:attribute>
                    </input>
                    <input id="field_org_name" name="organization_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="booking/organization_name"/></xsl:attribute>
                    </input>
                    <div id="org_container"/>
                </div>
            </dd>
            <dt><label for="field_group"><xsl:value-of select="php:function('lang', 'Group')"/></label></dt>
            <dd>
                <div id="group_container"><xsl:value-of select="php:function('lang', 'Loading...')"/></div>
            </dd>
            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')"/></label></dt>
            <dd>
                <div class="datetime-picker">
                <input id="field_from" name="from_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="booking/from_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')"/></label></dt>
            <dd>
                <div class="datetime-picker">
                <input id="field_to" name="to_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="booking/to_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_cost"><xsl:value-of select="php:function('lang', 'Cost')" /></label></dt>
            <dd><input id="field_cost" name="cost" type="text" value="{booking/cost}"/></dd>
        </dl>
		<dl class="form-col">
			<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Target audience')" /></label></dt>
			<dd>
				<ul>
					<xsl:for-each select="audience">
						<li>
							<input type="checkbox" name="audience[]">
								<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
								<xsl:if test="../booking/audience=id">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<label><xsl:value-of select="name"/></label>
						</li>
					</xsl:for-each>
				</ul>
			</dd>
			<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label></dt>
			<dd>
				<table id="agegroup">
					<tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
					    <th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
					<xsl:for-each select="agegroups">
						<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
						<tr>
							<th><xsl:value-of select="name"/></th>
							<td>
								<input type="text">
									<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
								</input>
							</td>
							<td>
								<input type="text">
									<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
								</input>
							</td>
						</tr>
					</xsl:for-each>
				</table>
			</dd>
			<dt><xsl:value-of select="php:function('lang', 'SMS total')" /></dt>
			<dd>
				<input type="text" name="sms_total">
					<xsl:attribute name="value"><xsl:value-of select="booking/sms_total"/></xsl:attribute>
				</input>
			</dd>
			<dt><label for="field_reminder"><xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" /></label></dt>
			<dd>
				<select name="reminder" id="field_reminder">
					<xsl:if test="booking/reminder = 1">
						<option value="1" selected="selected"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
						<option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
						<option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
						<option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
					</xsl:if>
					<xsl:if test="booking/reminder = 0">
						<option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
						<option value="0" selected="selected"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
						<option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
						<option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
					</xsl:if>
					<xsl:if test="booking/reminder = 2">
						<option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
						<option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
						<option value="2" selected="selected"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
						<option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
					</xsl:if>
					<xsl:if test="booking/reminder = 3">
						<option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
						<option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
						<option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
						<option value="3" selected="selected"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
					</xsl:if>
				</select>
			</dd>
		</dl>
		<div style="clear: both" />
		<dl class="form">
			<dt><label for="field_mail"><xsl:value-of select="php:function('lang', 'Inform contact persons')" /></label></dt>
			<dd>
				<label><xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" /></label><br />
				<textarea id="field_mail" name="mail" class="full-width"></textarea>
			</dd>
		</dl>
        <div class="form-buttons">
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')"/>
            </a>
        </div>
    </form>
    </div>
	
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="booking/season_id"/>';
        YAHOO.booking.group_id = '<xsl:value-of select="booking/group_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script>
</xsl:template>
