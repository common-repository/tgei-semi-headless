/**
 * This file is part of TGEI Semi Headless
 * TGEI Semi Headless is licensed with GPLv2
 * Copyright (C) 2024  Too Good Enterprises Inc.
 */
class TGEI_SemiHeadless_Settings
{
  static showShadowBox(evt, box, img)
  {
    evt.stopImmediatePropagation();
    evt.preventDefault();
    let shadowBox = document.getElementById(box);
    let imgUrl = img.getAttribute("src");
    let imgWidth = img.naturalWidth;
    let imgHeight = img.naturalHeight;
    shadowBox.innerHTML = `
      <div>
      <span class="tgei-close" onclick="TGEI_SemiHeadless_Settings.hideShadowBox(event, 'tgei-shadowbox');">X</span>
      <img src="${imgUrl}" width="${imgWidth}" height="${imgHeight}" style="--width: ${imgWidth}px; --height: ${imgHeight}px;" />
      </div>
    `;
    shadowBox.classList.remove("tgei-hide");
  }

  static hideShadowBox(evt, box)
  {
    evt.preventDefault();
    let shadowBox = document.getElementById(box);
    shadowBox.classList.add("tgei-hide");
    shadowBox.innerHTML = "";
  }
}
