<table border="0" width="210" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td align="center" class="sideback"><div style="height: 20px; margin-top: 3px;" class="G10BOLD"><a class="ps3" href="month.php?cal={CAL}&amp;getdate={MONTH_DATE}">{MONTH_TITLE}</a></div></td>
	</tr>
	<tr>
		<td>
			<table border="0" width="210" cellspacing="1" cellpadding="0" class="yearmonth">
				<tr>
					<!-- loop weekday on -->
					<td width="30" height="14" class="dateback" align="center"><font class="V9BOLD">{LOOP_WEEKDAY}</font></td>
					<!-- loop weekday on -->
				</tr>
				<!-- loop monthweeks on -->
				<tr height="30">
					<!-- loop monthdays on -->
					<!-- switch notthismonth on -->
					<td width="30" height="30" align="right" valign="top" class="monthoff" onmouseover="this.style.backgroundColor="#DDDDDD"" onmouseout="this.style.backgroundColor="#F2F2F2"" onclick="window.location.href='day.php?cal=all_calendars_combined971&amp;getdate=20031228'">
						<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}"><font class="V9G">28</font></a>
					</td>
					<!-- switch notthismonth off -->
					<!-- switch ismonth on -->
					<td width="30" height="30" align="right" valign="top" class="monthreg" onmouseover="this.style.backgroundColor="#DDDDDD"" onmouseout="this.style.backgroundColor="#FFFFFF"" onclick="window.location.href='day.php?cal=all_calendars_combined971&amp;getdate=20040101'">
						<div align="right" valign="top" class="V9">
						<div align="center" valign="top">
							<!-- switch allday on -->
							<img src="styles/silver/allday_dot.gif" alt=" " width="11" height="10" border="0">
							<!-- switch allday off -->
							<!-- switch event on -->
							<img src="styles/silver/event_dot.gif" alt=" " width="11" height="10" border="0">
							<!-- switch event off -->
						</div>
					</td>
					<!-- switch ismonth on -->
					<!-- loop monthdays off -->
				</tr>
				<!-- loop monthweeks off -->	
			</table>
		</td>
	</tr>
</table>