import React, {FC, useEffect, useState} from 'react';
import {CheckRoundIcon, Helper, SectionTitle} from 'akeneo-design-system';
import {useTranslate} from '../../../shared/translate';
import styled from 'styled-components';
import {ConnectedApp} from '../../../model/Apps/connected-app';
import {ScopeItem, ScopeList} from '../ScopeList';
import ScopeMessage from '../../../model/Apps/scope-message';
import {useFetchConnectedAppScopeMessages} from '../../hooks/use-fetch-connected-app-scope-messages';
import {useFeatureFlags} from '../../../shared/feature-flags';
import {ConnectedAppScopeListIsLoading} from './ConnectedAppScopeListIsLoading';

const ScopeListContainer = styled.div`
  margin: 10px 20px;
`;

type Props = {
    connectedApp: ConnectedApp;
};

export const ConnectedAppSettings: FC<Props> = ({connectedApp}) => {
    const translate = useTranslate();
    const featureFlag = useFeatureFlags();
    const fetchConnectedAppScopeMessages = useFetchConnectedAppScopeMessages(connectedApp.connection_code);
    const [connectedAppScopeMessages, setConnectedAppScopeMessages] = useState<ScopeMessage[] | null | false>(null);

    useEffect(() => {
        if (!featureFlag.isEnabled('marketplace_activate')) {
            setConnectedAppScopeMessages(false);
            return;
        }

        fetchConnectedAppScopeMessages()
            .then(setConnectedAppScopeMessages)
            .catch(() => setConnectedAppScopeMessages(false));
    }, [fetchConnectedAppScopeMessages]);

    const informationLinkAnchor = translate(
        'akeneo_connectivity.connection.connect.connected_apps.edit.settings.authorizations.information_link_anchor'
    );

    return (
        <>
            <SectionTitle>
                <SectionTitle.Title>{translate('akeneo_connectivity.connection.connect.connected_apps.edit.settings.authorizations.title')}</SectionTitle.Title>
            </SectionTitle>
            <Helper level='info'>
                <div
                    dangerouslySetInnerHTML={{
                        __html: translate(
                            'akeneo_connectivity.connection.connect.connected_apps.edit.settings.authorizations.information',
                            {link: `<a href='https://help.akeneo.com/pim/serenity/articles/how-to-connect-my-pim-with-apps.html#all-editions-authorization-step' target='_blank'>${informationLinkAnchor}</a>`}
                        ),
                    }}
                />
            </Helper>

            {null === connectedAppScopeMessages && <ConnectedAppScopeListIsLoading />}
            {false !== connectedAppScopeMessages && null !== connectedAppScopeMessages && (
                <ScopeListContainer>
                    {0 === connectedAppScopeMessages.length ? (
                        <ScopeItem key='0' fontSize='default'>
                            <CheckRoundIcon size={24} />
                            {translate('akeneo_connectivity.connection.connect.connected_apps.edit.settings.authorizations.no_scope')}
                        </ScopeItem>
                    ) : (
                        <ScopeList scopeMessages={connectedAppScopeMessages} itemFontSize='default' />
                    )}
                </ScopeListContainer>
            )}
        </>
    );
};
