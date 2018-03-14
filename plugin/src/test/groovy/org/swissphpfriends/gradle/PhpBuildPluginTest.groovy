package org.swissphpfriends.gradle

import org.junit.Test
import org.gradle.testfixtures.ProjectBuilder
import org.gradle.api.Project
import org.swissphpfriends.gradle.task.ComposerInstall
import org.swissphpfriends.gradle.task.PhpUnit

import static org.junit.Assert.*


class PhpBuildPluginTest {
    @Test
    public void pluginAddsComposerInstallTaskToProject() {
        Project project = ProjectBuilder.builder().build()
        project.apply plugin: 'php-build'

        assertTrue(project.tasks.composerInstall instanceof ComposerInstall)
    }

    @Test
    public void pluginAddsPhpUnitTaskToProject() {
        Project project = ProjectBuilder.builder().build()
        project.apply plugin: 'php-build'

        assertTrue(project.tasks.phpunit instanceof PhpUnit)
    }
}