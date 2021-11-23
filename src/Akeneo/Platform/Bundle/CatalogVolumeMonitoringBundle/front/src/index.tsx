import React from 'react';
import ReactDOM from 'react-dom';
import {ThemeProvider} from 'styled-components';
import {pimTheme} from 'akeneo-design-system';
import {MicroFrontendDependenciesProvider, Routes, Translations} from '@akeneo-pim-community/shared';
import {routes} from './routes.json';
import translations from './translations.json';
import {CatalogVolumeMonitoringApp} from './feature';
import {FakePIM} from './FakePIM';
// import {getMockCatalogVolume} from './feature/fetcher/getMockCatalogVolume';
import {getCatalogVolume} from './feature/fetcher/getCatalogVolume';

ReactDOM.render(
  <React.StrictMode>
    <ThemeProvider theme={pimTheme}>
      <MicroFrontendDependenciesProvider routes={routes as Routes} translations={translations as Translations}>
        <FakePIM>
          <CatalogVolumeMonitoringApp getCatalogVolumes={getCatalogVolume} />
        </FakePIM>
      </MicroFrontendDependenciesProvider>
    </ThemeProvider>
  </React.StrictMode>,
  document.getElementById('root')
);
