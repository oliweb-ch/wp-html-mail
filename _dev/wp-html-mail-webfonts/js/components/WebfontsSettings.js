import { useState, useEffect } from "@wordpress/element";

import {
	Button,
	SelectControl,
	Panel,
	PanelBody,
	PanelRow,
	Card,
	CardHeader,
	CardBody,
	Spinner,
	Notice,
	Animate,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";


export default function WebfontSelector({templateDesignerContext}) {
	const [fontSettings, setFontSettings] = useState({});
	const [fallbackFonts, setFallbackFonts] = useState([]);
	const [googleFonts, setGoogleFonts] = useState([]);
	const [previewText, setPreviewText] = useState(
		"Lorem ipsum dolor sit amet, consetetur sadipscing elitr"
	);
	const [rerenderKey, setRerenderKey] = useState(1);
	const [isSaving, setIsSaving] = useState(false);
	const [isLoading, setIsLoading] = useState(true);
	const [showSaveSuccess, setShowSaveSuccess] = useState(false);

	const loadSettings = () => {
		var request = new Request(
			window.mailTemplateDesigner.restUrl + "webfontsettings",
			{
				method: "GET",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": window.mailTemplateDesigner.nonce
				},
				credentials: "same-origin"
			});
		fetch(request)
			.then((resp) => resp.json())
			.then((data) => {
				const newSettings = { ...data };
				if (!("fontsets" in newSettings)) newSettings.fontsets = [];
				for (let fontsetIndex = 0; fontsetIndex <= 2; fontsetIndex++) {
					if (newSettings.fontsets.length <= fontsetIndex)
						newSettings.fontsets[fontsetIndex] = {
							googleFont: "",
							fallbackFont: "",
						};
				}
				if ("googlefonts" in newSettings) {
					const newGoogleFonts = [];
					newSettings['googlefonts'].forEach((font) => {
						newGoogleFonts.push({ label: font.family, value: font.family });
					});
					delete newSettings['googlefonts'];
					setGoogleFonts(newGoogleFonts)
				}
				if ("fallbackfonts" in newSettings) {
          const newFallbackFonts = [...newSettings['fallbackfonts']];
          delete newSettings['fallbackfonts'];
					setFallbackFonts(newFallbackFonts)
				}
					
				setFontSettings(newSettings);
				setIsLoading(false);
			});
	};

	const saveSettings = () => {
		setIsSaving(true);
		const preparedSettings = { ...fontSettings };
		preparedSettings.fontsets.forEach((fontset, fontsetIndex) => {
			if (
				preparedSettings.fontsets[fontsetIndex].googleFont &&
				preparedSettings.fontsets[fontsetIndex].fallbackFont
			) {
				preparedSettings.fontsets[fontsetIndex].name =
					"Fontset #" +
					(fontsetIndex + 1) +
					": " +
					preparedSettings.fontsets[fontsetIndex].googleFont;

				preparedSettings.fontsets[fontsetIndex].cssvalue =
					'"' +
					preparedSettings.fontsets[fontsetIndex].googleFont +
					'",' +
					preparedSettings.fontsets[fontsetIndex].fallbackFont;
			} else {
				preparedSettings.fontsets[fontsetIndex].name = "";
				preparedSettings.fontsets[fontsetIndex].cssvalue = "";
      }
		});
		var request = new Request(
			window.mailTemplateDesigner.restUrl + "webfontsettings",
			{
				method: "POST",
				body: JSON.stringify(preparedSettings),
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": window.mailTemplateDesigner.nonce
				},
			}
		);
		fetch(request).then((resp) => {
      setIsSaving(false);
      templateDesignerContext.setInfoMessage(
        __('Your settings have been saved.', 'wp-html-mail')
        + __('Refreshing page and loading stylesheet now...', 'wp-html-mail-webfonts')
      )
      setTimeout(() => {
        window.location.reload();
      }, 1000)
      setTimeout(() => {
        templateDesignerContext.setInfoMessage("");
      }, 7000)
		});
	};

	const refreshGoogleFontsStylesheetURL = (settings) => {
		let url = "";

		settings.fontsets.forEach((fontset) => {
			if (fontset.googleFont)
				url += "&family=" + fontset.googleFont.replace(" ", "+");
		});
		if (url === "") url = false;
		else url = "https://fonts.googleapis.com/css2?display=swap" + url;
		return url;
	};

	useEffect(() => {
		loadSettings();
	}, []);

	const renderFontset = (fontsetIndex) => {

		return (
			<PanelBody
				title={
					__("Fontset", "wp-html-mail-webfonts") +
					" #" +
					(fontsetIndex + 1) +
					(fontSettings.fontsets[fontsetIndex].googleFont
						? ": " + fontSettings.fontsets[fontsetIndex].googleFont
						: "")
				}
				initialOpen={fontsetIndex === 0}
				className="fontset"
				key={"fontset-" + fontsetIndex}
			>
				<PanelRow className="fontsetrow">
					<div className="fontselectcol">
						<SelectControl
							label={__(
								"Google Webfont",
								"wp-html-mail-webfonts"
							)}
							key={
								"google-font-select-" +
								fontsetIndex +
								rerenderKey
							}
							value={fontSettings.fontsets[fontsetIndex].googleFont}
							options={[
								{ value: "", label: "-" },
								...googleFonts,
							]}
							onChange={(font) => {
								setFontSettings((settings) => {
									settings.fontsets[
										fontsetIndex
									].googleFont = font;
									settings.googleFontsStylesheetURL = refreshGoogleFontsStylesheetURL(
										settings
									);
									return settings;
								});

								setRerenderKey(
									(rerenderKey) => rerenderKey + 1
								);
							}}
						/>
					</div>
					<div className="previewcol">
						{fontSettings.fontsets[fontsetIndex].googleFont && (
							<input
								className="previewtext"
								type="text"
								style={{
									fontFamily:
										fontSettings.fontsets[fontsetIndex]
											.googleFont,
								}}
								value={previewText}
								onChange={(e) => {
									setPreviewText(e.target.value);
								}}
							/>
						)}
					</div>
				</PanelRow>
				<PanelRow className="fontsetrow">
					<div className="fontselectcol">
						<SelectControl
							label={__(
								"Alternative font",
								"wp-html-mail-webfonts"
							)}
							key={
								"fallback-font-select-" +
								fontsetIndex +
								rerenderKey
							}
							value={fontSettings.fontsets[fontsetIndex].fallbackFont}
							options={[
								{ value: "", label: "-" },
								...fallbackFonts,
							]}
							onChange={(font) => {
								setFontSettings((settings) => {
									settings.fontsets[
										fontsetIndex
									].fallbackFont = font;
									return settings;
								});
								setRerenderKey(
									(rerenderKey) => rerenderKey + 1
								);
							}}
						/>
					</div>
					<div className="previewcol">
						{fontSettings.fontsets[fontsetIndex].fallbackFont && (
							<input
								className="previewtext"
								type="text"
								style={{
									fontFamily:
										fontSettings.fontsets[fontsetIndex]
											.fallbackFont,
								}}
								value={previewText}
								onChange={(e) => {
									setPreviewText(e.target.value);
								}}
							/>
						)}
					</div>
				</PanelRow>
			</PanelBody>
		);
	};

	if (isLoading || !fontSettings)
		return (
			<div className="mail-loader">
				<Spinner />
			</div>
		);
	else
		return (
			<div id="wp-html-mail-webfonts" className="mail-pluginSettings">
				<div className="settings-editor">
          <Card className="mail-settings-content">
            <CardHeader>
              <h3>{__("Choose your fonts", "wp-html-mail-webfonts")}</h3>
            </CardHeader>
            <CardBody className="description">
              <p>
                {__(
                  "Create up to three fontsets. Each of them consists of a webfont and a fallback font for email clients which do not support webfonts. Once defined here you can use them in your template and in our WooCommerce extension like any other font.",
                  "wp-html-mail-webfonts"
                )}
              </p>
            </CardBody>

            {"googleFontsStylesheetURL" in fontSettings &&
              fontSettings.googleFontsStylesheetURL && (
                <link
                  key={"google-stylesheet-" + rerenderKey}
                  rel="stylesheet"
                  type="text/css"
                  href={fontSettings.googleFontsStylesheetURL}
                />
              )}
            <CardBody>
              <Panel className="fontsets-panel">
                {fontSettings.fontsets &&
                  fontSettings.fontsets.length > 0 &&
                  fontSettings.fontsets.map((fontset, fontsetIndex) =>
                    renderFontset(fontsetIndex)
                  )}
              </Panel>
            </CardBody>
          </Card>
				</div>
				<div className="info-sidebar">
					<Card>
						<CardHeader>
							<h3>
								{__(
									"Webfonts in emails",
									"wp-html-mail-webfonts"
								)}
							</h3>
						</CardHeader>
						<CardBody className="description">
							<p>
								{__(
									"As not all email clients can handle webfonts those who don't will show a different font family. To avoid each email client to pick its own favorite font you can define an alternativ font for each webfont you choose. Please keep in mind that your fallback font uses the same font size and style as the main one.",
									"wp-html-mail-webfonts"
								)}
							</p>
							<p>
								{__(
									"Currently these email clients support webfonts:",
									"wp-html-mail-webfonts"
								)}
							</p>
							<ul>
								<li>Apple Mail</li>
								<li>iOS Mail</li>
								<li>Samsung Mail</li>
								<li>Outlook for Mac</li>
								<li>Outlook App</li>
								<li>Thunderbird</li>
							</ul>
						</CardBody>
					</Card>
        </div>
        <div className="save-button-pane-bottom">
					<Button
						isPrimary
						isBusy={isSaving}
            onClick={(e) => {
              e.preventDefault();
              saveSettings();
						}}
					>
						{__("Save settings", "wp-html-mail")}
					</Button>
				</div>
			</div>
		);
}
